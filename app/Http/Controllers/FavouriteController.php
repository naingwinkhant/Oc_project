<?php

namespace App\Http\Controllers;

use App\Cart\FavouriteService;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavouriteController extends Controller
{
    public function __construct(private readonly FavouriteService $favourites) {}

    public function index(): View
    {
        return view('favourites.index', [
            'products' => $this->favourites->products(),
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $added = $this->favourites->toggle($product);

        $back = $data['return_to'] ?? route('favourites.index');

        return redirect()->to($back)->with(
            'status',
            $added ? $product->name.' saved to your favourites.' : $product->name.' removed from your favourites.'
        );
    }

    public function clear(): RedirectResponse
    {
        $this->favourites->clear();

        return back()->with('status', 'Your favourites list is now empty.');
    }
}
