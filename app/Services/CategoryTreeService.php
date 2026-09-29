<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryTreeService
{
    /**
     * Recalculate depth + materialised path for a category and its subtree.
     */
    public function rebuildSubtree(int $categoryId): void
    {
        $category = Category::find($categoryId);

        if (! $category) {
            return;
        }

        $path = $category->parent
            ? rtrim($category->parent->path, '/').'/'.$category->id.'/'
            : '/'.$category->id.'/';

        $category->forceFill([
            'depth' => max(0, substr_count($path, '/') - 2),
            'path' => $path,
        ])->save();

        foreach (Category::where('parent_id', $category->id)->get() as $child) {
            $this->rebuildSubtree($child->id);
        }
    }

    public function rebuildAll(): void
    {
        foreach (Category::roots()->get() as $root) {
            $this->rebuildSubtree($root->id);
        }
    }

    /**
     * Nest a flat ordered collection of categories into a tree.
     *
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, array<string, mixed>>
     */
    public function nest(Collection $categories): Collection
    {
        $byParent = $categories->groupBy(fn (Category $c) => $c->parent_id ?? 0);

        $counts = $categories
            ->groupBy('id')
            ->map(fn (Collection $group) => (int) $group->first()->products_count);

        $build = function (int $parentKey) use (&$build, $byParent, $counts): array {
            return $byParent->get($parentKey, collect())
                ->map(function (Category $category) use (&$build, $counts) {
                    $ids = $category->descendantIds();
                    $total = collect($ids)->sum(fn (int $id) => $counts->get($id, 0));

                    return [
                        ...$category->toArray(),
                        'children' => $build($category->id),
                        'direct_products_count' => (int) $counts->get($category->id, 0),
                        'total_products_count' => $total,
                    ];
                })
                ->values()
                ->all();
        };

        return collect($build(0));
    }

    /**
     * True when $candidateId sits inside $categoryId's subtree.
     */
    public function isDescendant(int $categoryId, int $candidateId): bool
    {
        if ($categoryId === $candidateId) {
            return true;
        }

        return in_array($candidateId, Category::find($categoryId)?->descendantIds() ?? [], true);
    }
}
