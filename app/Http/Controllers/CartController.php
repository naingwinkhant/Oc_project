<?php

namespace App\Http\Controllers;

use App\Cart\CartService;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(): View
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'blocked' => $this->cart->blockedItems(),
            'summary' => $this->cart->summary(),
            'amountUntilFree' => $this->cart->amountUntilFreeDelivery(),
            'isFreeDelivery' => $this->cart->isFreeDelivery(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->active()->findOrFail($data['product_id']);

        try {
            $this->cart->add($product, (int) ($data['quantity'] ?? 1));
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.create');
        }

        return back()->with('status', $product->name.' added to your cart.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->setQuantity((int) $data['product_id'], (int) $data['quantity']);

        if ((int) $data['quantity'] === 0) {
            return back()->with('status', 'Item removed from your cart.');
        }

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(int $product): RedirectResponse
    {
        $this->cart->remove($product);

        return back()->with('status', 'Item removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->route('catalog.index')->with('status', 'Your cart is now empty.');
    }
}
