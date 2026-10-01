<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\TeamAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * New accounts waiting to be accepted.
 *
 * A registration cannot do anything at all until it is accepted here, so this
 * queue is the only way a stranger becomes able to sign in. An administrator or
 * a manager works through it; an accepted account never comes back.
 */
class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.approvals.index', [
            'pending' => User::query()
                ->where('status', AccountStatus::Pending)
                ->orderBy('created_at')
                ->get(),
            'decided' => User::query()
                ->whereIn('status', [AccountStatus::Approved, AccountStatus::Rejected])
                ->whereNotNull('decided_at')
                ->with('decider:id,name')
                ->latest('decided_at')
                ->limit(10)
                ->get(),
            'counts' => [
                'pending' => User::query()->where('status', AccountStatus::Pending)->count(),
                'approved' => User::query()->where('status', AccountStatus::Approved)->count(),
                'rejected' => User::query()->where('status', AccountStatus::Rejected)->count(),
            ],
        ]);
    }

    /**
     * Accept: from now on this account can sign in and reach the staff page.
     */
    public function accept(Request $request, User $user): RedirectResponse
    {
        if (! $user->isPending()) {
            return back()->with('error', $user->name.' has already been looked at.');
        }

        $user->approve($request->user());

        // Decided, so the alert about the decision is no longer true.
        app(TeamAlertService::class)->clearForAccount($user->id);

        $this->log($request, 'accepted the account for '.$user->name, $user);

        return back()->with('success', $user->name.' was accepted and can now sign in.');
    }

    /**
     * Turn down: the account stays on file but can never sign in.
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        if (! $user->isPending()) {
            return back()->with('error', $user->name.' has already been looked at.');
        }

        $user->reject($request->user());

        // Also decided, so the same alert goes away.
        app(TeamAlertService::class)->clearForAccount($user->id);

        $this->log($request, 'turned down the account for '.$user->name, $user);

        return back()->with('success', $user->name.' was turned down and cannot sign in.');
    }

    private function log(Request $request, string $description, User $subject): void
    {
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'subject_type' => User::class,
            'subject_id' => $subject->id,
            'description' => $request->user()->name.' '.$description,
        ]);
    }
}
