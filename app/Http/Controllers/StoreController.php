<?php

namespace App\Http\Controllers;

use App\Support\StoreContent;
use Illuminate\View\View;

/**
 * The two customer-facing pages that are not the catalogue.
 *
 * Both are open to everyone, signed in or not, because they are the pages a
 * shopper reads before deciding to order.
 */
class StoreController extends Controller
{
    public function services(): View
    {
        return view('store.services', [
            'services' => StoreContent::services(),
            'rules' => StoreContent::rules(),
        ]);
    }

    public function information(): View
    {
        return view('store.information', [
            'info' => StoreContent::information(),
        ]);
    }
}
