<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\TeamAlert;
use App\Notifications\BellService;
use App\Notifications\NotificationService;
use App\Notifications\TeamAlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly TeamAlertService $alerts,
        private readonly BellService $bell,
    ) {}

    public function dismiss(Request $request, Notice $notice): JsonResponse|RedirectResponse
    {
        $this->notifications->dismiss($notice);

        return $this->reply($request, 'Notification cleared.');
    }

    public function dismissAlert(Request $request, TeamAlert $alert): JsonResponse|RedirectResponse
    {
        $this->alerts->dismiss($alert);

        return $this->reply($request, 'Alert cleared.');
    }

    /**
     * Clears both halves of the bell, because the panel shows both.
     */
    public function dismissAll(Request $request): JsonResponse|RedirectResponse
    {
        $this->notifications->dismissAll();

        if ($request->user()?->isStaff()) {
            $this->alerts->dismissAll();
        }

        return $this->reply($request, 'All notifications cleared.');
    }

    /**
     * The bell posts with fetch and wants the new badge back; anything else is a
     * plain form post and wants a redirect.
     */
    private function reply(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'badge' => $this->bell->badge()]);
        }

        return back()->with('status', $message);
    }
}
