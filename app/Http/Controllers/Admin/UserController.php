<?php

namespace App\Http\Controllers\Admin;

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
        $data = $request->validated();
        $data['password'] = $data['password'] ?? 'password';

        $user = User::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Created user account for '.$user->name,
        ]);

        return redirect()->route('admin.users.index')->with('success', $user->name.' can now sign in.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
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
