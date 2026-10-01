<?php

namespace App\Http\Controllers;

use App\History\ViewHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __construct(private readonly ViewHistoryService $history) {}

    /**
     * What you looked at on this device.
     *
     * Orders are deliberately not listed here. Nobody has to sign in to order,
     * so an order belongs to the basket it was placed from rather than to an
     * account, and there is no account whose orders these could be. Staff see
     * every order in the dashboard instead.
     */
    public function index(): View
    {
        return view('history.index', [
            'viewed' => $this->history->products(),
            'viewedCount' => $this->history->count(),
        ]);
    }

    public function clear(): RedirectResponse
    {
        $this->history->clear();

        return back()->with('status', 'Your browsing history was cleared.');
    }
}
