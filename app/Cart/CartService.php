<?php

namespace App\Cart;

use App\Models\Product;
use App\Support\Delivery;
use App\Support\Money;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping cart.
 *
 * A guest cart lives in the session; when someone signs in the contents are
 * merged into the same session cart, so nothing is lost at the till.
 */
class CartService
{
    private const SESSION_KEY = 'cart.items';

    public function __construct(private readonly Session $session) {}

    /**
     * @return Collection<int, array{product: Product, quantity: int, line_total: int}>
     */
    public function items(): Collection
    {
        $raw = (array) $this->session->get(self::SESSION_KEY, []);

        if ($raw === []) {
            return collect();
        }

        $ids = array_keys($raw);
        $products = Product::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $items = collect();

        foreach ($raw as $id => $quantity) {
            $product = $products->get((int) $id);

            // Drop anything that was deleted, hidden or is no longer purchasable.
            if (! $product || ! $product->is_active) {
                continue;
            }

            $quantity = max(1, min((int) $quantity, 99));

            $items->push([
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => $product->effectivePrice() * $quantity,
            ]);
        }

        return $items;
    }

    public function add(Product $product, int $quantity = 1): void
    {
        if ($product->isExpired()) {
            throw new \DomainException($product->name.' expired on '.$product->expires_at->format('j M Y').' and cannot be added.');
        }

        if ($product->isComingSoon()) {
            throw new \DomainException($product->name.' is not on the shelf until '.$product->available_from->format('j M Y').'.');
        }

        if ($product->isOutOfStock()) {
            throw new \DomainException($product->name.' is out of stock.');
        }

        $raw = (array) $this->session->get(self::SESSION_KEY, []);
        $current = (int) ($raw[$product->id] ?? 0);

        $raw[$product->id] = max(1, min($current + max(1, $quantity), 99));

        $this->session->put(self::SESSION_KEY, $raw);
    }

    public function setQuantity(int $productId, int $quantity): void
    {
        $raw = (array) $this->session->get(self::SESSION_KEY, []);

        if (! array_key_exists($productId, $raw)) {
            return;
        }

        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        $raw[$productId] = max(1, min($quantity, 99));
        $this->session->put(self::SESSION_KEY, $raw);
    }

    public function remove(int $productId): void
    {
        $raw = (array) $this->session->get(self::SESSION_KEY, []);
        unset($raw[$productId]);
        $this->session->put(self::SESSION_KEY, $raw);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * Lines that cannot be sold — expired, not yet landed, or out of stock.
     *
     * @return Collection<int, array{product: Product, quantity: int, line_total: int, reason: string}>
     */
    public function blockedItems(): Collection
    {
        return $this->items()->filter(function (array $item) {
            return ! $item['product']->isSellable();
        })->map(function (array $item) {
            $product = $item['product'];

            $item['reason'] = match (true) {
                $product->isExpired() => 'Expired on '.$product->expires_at->format('j M Y'),
                $product->isComingSoon() => 'On the shelf from '.$product->available_from->format('j M Y'),
                $product->isOutOfStock() => 'Out of stock',
                default => 'No longer sold',
            };

            return $item;
        })->values();
    }

    public function hasBlockedItems(): bool
    {
        return $this->blockedItems()->isNotEmpty();
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum('line_total');
    }

    /**
     * Without a township the cart can only quote a range; checkout resolves the
     * exact zone once the customer picks one.
     */
    public function deliveryFee(?string $township = null): int
    {
        return Delivery::feeFor($township, $this->subtotal());
    }

    public function total(?string $township = null): int
    {
        return $this->subtotal() + $this->deliveryFee($township);
    }

    public function isFreeDelivery(?string $township = null): bool
    {
        return Delivery::isFree($this->subtotal());
    }

    public function amountUntilFreeDelivery(): int
    {
        return Delivery::amountUntilFree($this->subtotal());
    }

    public function summary(?string $township = null): array
    {
        $subtotal = $this->subtotal();
        $delivery = $this->deliveryFee($township);
        $range = Delivery::range($subtotal);

        return [
            'count' => $this->count(),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
            'subtotal_formatted' => Money::format($subtotal),
            'delivery_formatted' => Money::format($delivery),
            'total_formatted' => Money::format($subtotal + $delivery),
            'delivery_range' => $range,
            'delivery_known' => $township !== null,
        ];
    }
}
