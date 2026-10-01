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

    /**
     * Ceiling for one line, so a single request cannot ask for thousands.
     * Real stock is the actual limit; see availableFor().
     */
    private const MAX_PER_LINE = 99;

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

    /**
     * Add to the cart, never past what is on the shelf.
     *
     * @return int how many were actually added, so the caller can say so
     *
     * @throws \DomainException when the item cannot be bought at all
     */
    public function add(Product $product, int $quantity = 1): int
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
        $left = $this->availableFor($product, $current);

        if ($left < 1) {
            throw new \DomainException('You already have all '.$product->stock.' '.$product->unit.' of '.$product->name.' in your cart.');
        }

        // Clamp rather than refuse: a shopper tapping "add" twice should end up
        // with everything that is actually available, not an error message.
        $added = min(max(1, $quantity), $left);

        $raw[$product->id] = $current + $added;

        $this->session->put(self::SESSION_KEY, $raw);

        return $added;
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

        $product = Product::query()->find($productId);

        // The shelf can shrink under a cart that is already open, so the
        // quantity is capped against live stock, not the 99 ceiling.
        $cap = $product ? max(0, $product->stock) : self::MAX_PER_LINE;

        $raw[$productId] = max(1, min($quantity, $cap, self::MAX_PER_LINE));
        $this->session->put(self::SESSION_KEY, $raw);
    }

    /**
     * How many more of this item may go in the cart, stock allowing.
     */
    public function availableFor(Product $product, ?int $alreadyInCart = null): int
    {
        $raw = (array) $this->session->get(self::SESSION_KEY, []);
        $current = $alreadyInCart ?? (int) ($raw[$product->id] ?? 0);

        return max(0, min($product->stock, self::MAX_PER_LINE) - $current);
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
     * Lines that cannot be sold as they stand — expired, not yet landed, out of
     * stock, or asking for more units than are left on the shelf.
     *
     * @return Collection<int, array{product: Product, quantity: int, line_total: int, reason: string}>
     */
    public function blockedItems(): Collection
    {
        return $this->items()->filter(function (array $item) {
            return ! $item['product']->isSellable()
                || $item['quantity'] > $item['product']->stock;
        })->map(function (array $item) {
            $product = $item['product'];

            $item['reason'] = match (true) {
                $product->isExpired() => 'Expired on '.$product->expires_at->format('j M Y'),
                $product->isComingSoon() => 'On the shelf from '.$product->available_from->format('j M Y'),
                $product->isOutOfStock() => 'Out of stock',
                $item['quantity'] > $product->stock => 'Only '.$product->stock.' '.$product->unit.' left',
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

    /**
     * How much the promotions are saving on this basket, in whole kyat.
     */
    public function savings(): int
    {
        return (int) $this->items()->sum(function (array $item) {
            $product = $item['product'];

            return $product->hasDiscount()
                ? ($product->price - $product->effectivePrice()) * $item['quantity']
                : 0;
        });
    }

    public function summary(?string $township = null): array
    {
        $subtotal = $this->subtotal();
        $delivery = $this->deliveryFee($township);
        $range = Delivery::range($subtotal);
        $savings = $this->savings();

        return [
            'count' => $this->count(),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
            'subtotal_formatted' => Money::format($subtotal),
            'delivery_formatted' => Money::format($delivery),
            'total_formatted' => Money::format($subtotal + $delivery),
            // What the same basket would cost at the shelf price.
            'undiscounted' => $subtotal + $savings,
            'undiscounted_formatted' => Money::format($subtotal + $savings),
            'savings' => $savings,
            'savings_formatted' => Money::format($savings),
            'delivery_range' => $range,
            'delivery_known' => $township !== null,
        ];
    }
}
