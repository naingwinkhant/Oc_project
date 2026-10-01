<?php

namespace App\Http\Controllers\Admin;

use App\Auth\PasswordLink;
use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly PasswordLink $links) {}

    public function index(Request $request): View
    {
        $users = User::query()
            ->search($request->string('q')->toString() ?: null)
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')->toString()))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->string('status')->toString() === 'active'))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => User::query()->count(),
            'active' => User::query()->active()->count(),
            'admins' => User::query()->where('role', Role::Admin->value)->count(),
            'inactive' => User::query()->where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User(['is_active' => true, 'role' => Role::Staff])]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create([
            ...$request->safe()->only(['username', 'name', 'email', 'phone', 'role', 'is_active']),
            // Nothing to guess: the holder sets their own through the link below.
            'password' => null,
            // Waiting to be accepted, so a username nobody agreed to is not a
            // way into the shop.
            'status' => AccountStatus::Pending,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Created user account for '.$user->name,
        ]);

        $link = $this->links->issue($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', $user->name.' can set a password, but the account is waiting to be accepted.')
            ->with('setPasswordLink', route('set-password', ['token' => $link]));
    }

    /**
     * Accept a pending account, which is what lets it sign in.
     */
    public function approve(Request $request, User $user): RedirectResponse
    {
        if ($user->isApproved()) {
            return back()->with('error', $user->name.' has already been accepted.');
        }

        $user->approve($request->user());

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Accepted the account for '.$user->name,
        ]);

        return back()->with('success', $user->name.' was accepted and can sign in once they set a password.');
    }

    /**
     * Withdraw an acceptance, locking that account out again.
     */
    public function revokeApproval(Request $request, User $user): RedirectResponse
    {
        if ($user->isPending()) {
            return back()->with('error', $user->name.' is already waiting to be accepted.');
        }

        $name = $user->name;
        $user->revokeApproval();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Withdrew acceptance for '.$name,
        ]);

        return back()->with('success', $name.' can no longer sign in.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['username', 'name', 'email', 'phone', 'role', 'is_active']);

        // Changing your own role is already refused by UserRequest; this covers
        // the other way the last administrator could be lost.
        $losesAdmin = $user->isAdmin()
            && (($data['role'] ?? $user->role->value) !== Role::Admin->value
                || array_key_exists('is_active', $data) && ! $data['is_active']);

        if ($losesAdmin && $this->remainingAdminCount($user) < 1) {
            return back()->with('error', 'At least one administrator must remain. Promote somebody else first.');
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Updated account '.$user->name,
        ]);

        return redirect()->route('admin.users.index')->with('success', $user->name.' was updated.');
    }

    /**
     * How many administrators would be left if this change went through.
     *
     * Guards the role-change and disable paths as well as delete, because losing
     * the last administrator through any of them means nobody can create users
     * or accept an account afterwards.
     */
    private function remainingAdminCount(User $subject): int
    {
        return User::query()
            ->where('role', Role::Admin->value)
            ->where('is_active', true)
            ->where('id', '!=', $subject->id)
            ->count();
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && User::query()->where('role', Role::Admin->value)->count() <= 1) {
            return back()->with('error', 'At least one administrator must remain.');
        }
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deleted',
            'subject_type' => User::class,
            'description' => 'Deleted account '.$name,
        ]);

        return back()->with('success', $name.'\'s account was deleted.');
    }
}
