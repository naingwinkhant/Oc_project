<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'color',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
            'depth' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function allProducts(): HasMany
    {
        return $this->hasMany(Product::class)->whereIn('category_id', $this->descendantIds());
    }

    /**
     * Ids of this category plus every category underneath it.
     *
     * @return array<int, int>
     */
    public function descendantIds(): array
    {
        $ids = [$this->id];
        $frontier = [$this->id];

        while ($frontier !== []) {
            $children = self::query()
                ->whereIn('parent_id', $frontier)
                ->pluck('id')
                ->all();

            $frontier = array_values(array_diff($children, $ids));
            $ids = array_merge($ids, $frontier);
        }

        return $ids;
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * The top-level department this classification sits under, so a sub-category
     * page can still highlight its parent in the navigation bar.
     */
    public function root(): self
    {
        $category = $this;

        while ($category->parent_id !== null) {
            $parent = $category->parent()->first();

            if (! $parent) {
                break;
            }

            $category = $parent;
        }

        return $category;
    }

    public function breadcrumb(): string
    {
        return $this->name;
    }

    public function imageUrl(): ?string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        return null;
    }

    public static function buildSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
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

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->orderBy('position');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return $query->where(fn (Builder $q) => $q->where('name', 'like', $like)->orWhere('slug', 'like', $like));
    }
}
