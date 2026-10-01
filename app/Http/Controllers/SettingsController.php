<?php

namespace App\Http\Controllers;

use App\Notifications\NotificationService;
use App\Support\StoreContent;
use App\Support\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * The shopper's own settings: how the shop looks, where to find their
     * history, and the store rules. Notices live in the bell, not on a page.
     */
    public function show(): View
    {
        return view('settings.index', [
            'theme' => Theme::forCurrentUser(),
            // The same sections the Services page renders, so the two can never
            // say different things.
            'rules' => StoreContent::rules(),
            'unreadCount' => app(NotificationService::class)->unreadCount(),
        ]);
    }

    /**
     * Day or night mode. Answers with JSON for the header toggle, and redirects
     * back to the settings page for the form.
     */
    public function updateTheme(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', Rule::in(Theme::options())],
        ]);

        auth()->user()?->forceFill(['theme' => $data['theme']])->save();

        if ($request->expectsJson()) {
            return response()->json(['theme' => $data['theme']]);
        }

        return back()->with('status', 'Your appearance preference was saved.');
    }
}
