<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'brand',
        'description',
        'unit',
        'weight',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'min_stock',
        'image',
        'produced_at',
        'expires_at',
        'available_from',
        'attributes',
        'is_active',
        'is_featured',
        'is_new',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'produced_at' => 'date',
            'expires_at' => 'date',
            'available_from' => 'date',
            'price' => 'integer',
            'sale_price' => 'integer',
            'cost_price' => 'integer',
            'weight' => 'decimal:3',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'views' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'product_supplier')->withPivot(['unit_cost', 'supplier_sku']);
    }

    public function imageUrl(): ?string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        return null;
    }

    /**
     * Attribution for the bundled demo photograph, if there is one.
     *
     * @return array{author: string, license: string, source: string}|null
     */
    public function imageCredit(): ?array
    {
        $file = resource_path('data/product-images.json');

        if (! is_file($file) || ! $this->slug) {
            return null;
        }

        $manifest = json_decode((string) file_get_contents($file), true) ?: [];
        $entry = $manifest[$this->slug] ?? null;

        if (! is_array($entry) || ! ($entry['source'] ?? null)) {
            return null;
        }

        return [
            'author' => (string) ($entry['author'] ?? ''),
            'license' => (string) ($entry['license'] ?? ''),
            'source' => (string) $entry['source'],
        ];
    }

    /**
     * Days of shelf life between production and expiry, when both are known.
     */
    public function shelfLifeDays(): ?int
    {
        if (! $this->produced_at || ! $this->expires_at) {
            return null;
        }

        return (int) $this->produced_at->diffInDays($this->expires_at);
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expires_at
            ? (int) now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false)
            : null;
    }

    public function isExpired(): bool
    {
        $days = $this->daysUntilExpiry();

        return $days !== null && $days < 0;
    }

    /**
     * expired | expiring | fresh | unknown
     */
    public function expiryStatus(): string
    {
        $days = $this->daysUntilExpiry();

        if ($days === null) {
            return 'unknown';
        }

        if ($days < 0) {
            return 'expired';
        }

        return $days <= (int) config('shop.freshness.expiring_soon_days', 30) ? 'expiring' : 'fresh';
    }

    public function expiryStatusTone(): string
    {
        return match ($this->expiryStatus()) {
            'expired' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            'expiring' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'fresh' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            default => 'bg-ink-100 text-ink-500 ring-ink-500/10',
        };
    }

    public function expiryStatusLabel(): string
    {
        $days = $this->daysUntilExpiry();

        return match ($this->expiryStatus()) {
            'expired' => 'Expired '.abs($days).'d ago',
            'expiring' => $days === 0 ? 'Expires today' : 'Expires in '.$days.'d',
            'fresh' => $days.'d left',
            default => 'No expiry',
        };
    }

    /**
     * The freshness story in words, so every screen describes a batch the same
     * way instead of each one inventing its own phrasing.
     *
     * @return array{packed: ?string, best_before: ?string, shelf_life: ?string, remaining: ?string, note: ?string}
     */
    public function freshness(): array
    {
        $days = $this->daysUntilExpiry();
        $shelf = $this->shelfLifeDays();

        $summary = [
            'packed' => $this->produced_at?->format('j M Y'),
            'best_before' => $this->expires_at?->format('j M Y'),
            'shelf_life' => $shelf === null
                ? null
                : $shelf.' '.Str::plural('day', $shelf).' shelf life',
            'remaining' => null,
            'note' => null,
        ];

        if ($this->isComingSoon()) {
            $summary['note'] = 'On the shelf '.$this->available_from->format('j M Y');

            return $summary;
        }

        if ($days === null) {
            $summary['note'] = 'No expiry date recorded';

            return $summary;
        }

        if ($days < 0) {
            $summary['remaining'] = 'Expired '.abs($days).' '.Str::plural('day', abs($days)).' ago';

            return $summary;
        }

        $summary['remaining'] = $days === 0
            ? 'Expires today'
            : $days.' '.Str::plural('day', $days).' left';

        return $summary;
    }

    /**
     * One short line, for the places a full sentence will not fit. Stays silent
     * when there is nothing worth saying.
     */
    public function freshnessLine(): ?string
    {
        $fresh = $this->freshness();

        if ($this->isComingSoon()) {
            return $fresh['note'];
        }

        $parts = array_filter([
            $fresh['best_before'] ? 'Best before '.$fresh['best_before'] : null,
            $fresh['remaining'],
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }

    /**
     * A saleable item must be on sale already and not past its expiry date.
     */
    public function isSellable(): bool
    {
        return $this->is_active
            && ! $this->isOutOfStock()
            && ! $this->isExpired()
            && ! $this->isComingSoon();
    }

    /**
     * The batch has been announced but has not landed on the shelf yet.
     */
    public function isComingSoon(): bool
    {
        return $this->available_from !== null
            && $this->available_from->startOfDay()->isFuture();
    }

    public function daysUntilAvailable(): ?int
    {
        if (! $this->isComingSoon()) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->available_from->startOfDay(), false);
    }

    public function scopeComingSoon(Builder $query): Builder
    {
        return $query->whereNotNull('available_from')
            ->whereDate('available_from', '>', now()->toDateString());
    }

    public function scopeExpiringSoon(Builder $query, ?int $days = null): Builder
    {
        $days ??= (int) config('shop.freshness.expiring_soon_days', 30);

        return $query->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now()->toDateString())
            ->whereDate('expires_at', '<=', now()->addDays($days)->toDateString());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now()->toDateString());
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    public function stockStatus(): string
    {
        return match (true) {
            $this->isOutOfStock() => 'out',
            $this->isLowStock() => 'low',
            default => 'ok',
        };
    }

    public function stockStatusTone(): string
    {
        return match ($this->stockStatus()) {
            'out' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            'low' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            default => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        };
    }

    public function stockStatusLabel(): string
    {
        return match ($this->stockStatus()) {
            'out' => 'Out of stock',
            'low' => 'Low stock',
            default => 'In stock',
        };
    }

    public function margin(): float
    {
        $cost = (int) $this->cost_price;

        if ($cost <= 0) {
            return 0.0;
        }

        return round(($this->effectivePrice() - $cost) / $cost * 100, 1);
    }

    /**
     * A sale price only counts when it is actually below the list price.
     */
    public function hasDiscount(): bool
    {
        return $this->sale_price !== null
            && (int) $this->sale_price > 0
            && (int) $this->sale_price < (int) $this->price;
    }

    /**
     * What the customer pays.
     */
    public function effectivePrice(): int
    {
        return $this->hasDiscount() ? (int) $this->sale_price : (int) $this->price;
    }

    /**
     * The price that gets struck through when the item is on promotion.
     */
    public function listPrice(): int
    {
        return (int) $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount() || (int) $this->price <= 0) {
            return 0;
        }

        return (int) round(((int) $this->price - (int) $this->sale_price) / (int) $this->price * 100);
    }

    public function discountAmount(): int
    {
        return $this->hasDiscount() ? (int) $this->price - (int) $this->sale_price : 0;
    }

    public function scopeOnSale(Builder $query): Builder
    {
        return $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
    }

    public function retailValue(): float
    {
        return round($this->effectivePrice() * $this->stock, 2);
    }

    public static function buildSku(string $prefix = 'SKU'): string
    {
        do {
            $sku = strtoupper($prefix).'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::query()->where('sku', $sku)->exists());

        return $sku;
    }

    public static function buildSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $counter = 2;

        while (self::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals(Builder $query): Builder
    {
        return $query->where('is_new', true);
    }

    public function isNewArrival(): bool
    {
        return (bool) $this->is_new;
    }

    public function daysSinceAdded(): int
    {
        return (int) $this->created_at->diffInDays(now());
    }

    public function scopeInCategory(Builder $query, ?Category $category): Builder
    {
        if (! $category) {
            return $query;
        }

        return $query->whereIn('category_id', $category->descendantIds());
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
        $digits = preg_replace('/\D/', '', $term) ?? '';

        return $query->where(function (Builder $query) use ($like, $digits) {
            $query->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('brand', 'like', $like)
                ->orWhere('description', 'like', $like);

            if ($digits !== '') {
                $query->orWhere('barcode', 'like', '%'.$digits.'%');
            }
        });
    }

    /**
     * Record a stock movement and keep the cached stock column in sync.
     */
    public function recordMovement(StockMovementType $type, int $quantity, ?string $reason = null, ?string $reference = null, ?int $userId = null): StockMovement
    {
        $signed = $type->sign() * abs($quantity);
        $balance = max(0, $this->stock + $signed);

        $movement = $this->stockMovements()->create([
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'quantity' => $signed,
            'balance_after' => $balance,
            'reason' => $reason,
            'reference' => $reference,
        ]);

        $this->forceFill(['stock' => $balance])->save();

        return $movement;
    }
}
