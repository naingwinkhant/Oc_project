<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CHART_DAYS = 30;

    public function index(): View
    {
        return view('admin.dashboard', [
            'health' => $this->shelfHealth(),
            'kpis' => $this->kpis(),
            'restock' => $this->restockQueue(),
            'movement' => $this->movementSeries(),
            'departments' => $this->departmentValue(),
            'movers' => $this->topMovers(),
            'activity' => $this->recentActivity(),
            'team' => $this->team(),
            'waitingAccounts' => $this->waitingAccounts(),
        ]);
    }

    /**
     * Accounts somebody has to decide on, newest first.
     *
     * Only an administrator or a manager ever sees this: accepting or turning
     * an account down is their call alone, and the page behind these buttons is
     * refused for anybody else.
     *
     * @return Collection<int, User>
     */
    private function waitingAccounts(): Collection
    {
        if (! auth()->user()?->canManageCatalog()) {
            return new Collection;
        }

        return User::query()
            ->pending()
            ->orderBy('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * The single headline answer: are the shelves healthy?
     */
    private function shelfHealth(): array
    {
        $total = Product::query()->count();
        $out = Product::query()->where('stock', '<=', 0)->count();
        $low = Product::query()
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $healthy = max(0, $total - $out - $low);

        $segments = [
            ['key' => 'ok', 'label' => 'Healthy', 'count' => $healthy, 'tone' => 'emerald'],
            ['key' => 'low', 'label' => 'Running low', 'count' => $low, 'tone' => 'amber'],
            ['key' => 'out', 'label' => 'Out of stock', 'count' => $out, 'tone' => 'rose'],
        ];

        foreach ($segments as $index => $segment) {
            $segments[$index]['percent'] = $total > 0 ? round($segment['count'] / $total * 100, 1) : 0.0;
        }

        $score = $total > 0 ? round($healthy / $total * 100) : 100;

        $headline = match (true) {
            $total === 0 => 'No goods in the catalogue yet',
            $out === 0 && $low === 0 => 'Every shelf is fully stocked',
            $out === 0 => 'A few items are running low',
            $out > 0 && $out > $low => 'Some shelves are empty — reorder today',
            default => 'Some items need reordering',
        };

        return [
            'score' => $score,
            'headline' => $headline,
            'total' => $total,
            'needsAction' => $low + $out,
            'segments' => $segments,
        ];
    }

    private function kpis(): array
    {
        $total = Product::query()->count();
        $active = Product::query()->active()->count();
        $stockValue = (int) Product::query()->sum(DB::raw('COALESCE(sale_price, price) * stock'));
        $costValue = (int) Product::query()->sum(DB::raw('cost_price * stock'));
        $margin = $costValue > 0 ? round((($stockValue - $costValue) / $costValue) * 100, 1) : 0.0;

        $departments = Category::query()->roots()->count();
        $aisles = Category::query()->whereNotNull('parent_id')->count();
        $unclassified = Product::query()->whereNull('category_id')->count();

        return [
            [
                'label' => 'Goods items',
                'value' => $total,
                'unit' => null,
                'icon' => 'box',
                'tone' => 'brand',
                'hint' => $active.' visible · '.($total - $active).' hidden',
                'link' => route('admin.products.index'),
            ],
            [
                'label' => 'Stock value',
                'value' => $stockValue,
                'money' => true,
                'unit' => null,
                'icon' => 'chart',
                'tone' => 'emerald',
                'hint' => $margin.'% potential margin',
                'link' => route('admin.products.index', ['sort' => 'stock_desc']),
            ],
            [
                'label' => 'Classifications',
                'value' => $departments,
                'unit' => 'dept',
                'icon' => 'layers',
                'tone' => 'sky',
                'hint' => $aisles.' aisles · '.$unclassified.' unclassified',
                'link' => route('admin.categories.index'),
            ],
            [
                'label' => 'Suppliers',
                'value' => Supplier::query()->count(),
                'unit' => null,
                'icon' => 'truck',
                'tone' => 'violet',
                'hint' => Supplier::query()->active()->count().' active',
                'link' => route('admin.suppliers.index'),
            ],
        ];
    }

    /**
     * Actionable restock list: most urgent items, with how close each is to its threshold.
     */
    private function restockQueue(): Collection
    {
        return Product::query()
            ->with('category')
            ->lowStock()
            ->orderByRaw('CASE WHEN stock = 0 THEN 0 ELSE 1 END')
            ->orderBy('stock')
            ->orderBy('min_stock')
            ->limit(6)
            ->get()
            ->map(function (Product $product) {
                $threshold = max(1, (int) $product->min_stock);
                $ratio = min(1, $product->stock / $threshold);
                $suggested = max($threshold * 2, (int) round($product->stock + $threshold));

                return [
                    'product' => $product,
                    'ratio' => round($ratio * 100),
                    'tone' => $product->stock <= 0 ? 'rose' : 'amber',
                    'shortage' => $threshold - $product->stock,
                    'suggested' => $suggested,
                ];
            });
    }

    /**
     * Daily goods-in vs goods-out for the last 30 days, ready to plot.
     */
    private function movementSeries(): array
    {
        $from = now()->subDays(self::CHART_DAYS - 1)->startOfDay();

        $rows = StockMovement::query()
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, type, SUM(ABS(quantity)) as qty')
            ->groupBy('day', 'type')
            ->get();

        $totals = [];

        foreach ($rows as $row) {
            $day = $row->day;
            $bucket = $totals[$day] ?? ['in' => 0, 'out' => 0];
            $bucket[$row->type->isPositive() ? 'in' : 'out'] += (int) $row->qty;
            $totals[$day] = $bucket;
        }

        $series = [];
        $peak = 0;

        for ($offset = self::CHART_DAYS - 1; $offset >= 0; $offset--) {
            $date = now()->subDays($offset);
            $key = $date->format('Y-m-d');
            $in = $totals[$key]['in'] ?? 0;
            $out = $totals[$key]['out'] ?? 0;

            $peak = max($peak, $in + $out);

            $series[] = [
                'date' => $key,
                'label' => $date->format('j M'),
                'short' => $date->format('j'),
                'in' => $in,
                'out' => $out,
                'total' => $in + $out,
            ];
        }

        $inTotal = array_sum(array_column($series, 'in'));
        $outTotal = array_sum(array_column($series, 'out'));

        return [
            'days' => $series,
            'peak' => max($peak, 1),
            'in' => $inTotal,
            'out' => $outTotal,
            'net' => $inTotal - $outTotal,
            'activeDays' => count(array_filter($series, fn (array $day) => $day['total'] > 0)),
        ];
    }

    /**
     * Share of stock value held by each department, including its aisles.
     */
    private function departmentValue(): Collection
    {
        $categories = Category::query()->orderBy('position')->get();
        $childrenOf = $categories->groupBy('parent_id');

        $subtree = function (int $id) use (&$subtree, $childrenOf): array {
            $ids = [$id];

            foreach ($childrenOf->get($id, collect()) as $child) {
                $ids = array_merge($ids, $subtree($child->id));
            }

            return $ids;
        };

        $stats = Product::query()
            ->whereNotNull('category_id')
            ->selectRaw('category_id, COUNT(*) as items, COALESCE(SUM(COALESCE(sale_price, price) * stock), 0) as value')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        $rows = $categories->whereNull('parent_id')->map(function (Category $root) use ($subtree, $stats) {
            $items = 0;
            $value = 0.0;

            foreach ($subtree($root->id) as $categoryId) {
                $stat = $stats->get($categoryId);

                if ($stat) {
                    $items += (int) $stat->items;
                    $value += (float) $stat->value;
                }
            }

            return [
                'name' => $root->name,
                'color' => $root->color,
                'items' => $items,
                'value' => $value,
            ];
        })->sortByDesc('value')->values();

        $total = max(0.0, (float) $rows->sum('value'));

        $withShare = $rows->map(function (array $row) use ($total) {
            $row['share'] = $total > 0 ? round($row['value'] / $total * 100, 1) : 0.0;
            $row['isOther'] = false;

            return $row;
        });

        $top = $withShare->take(8);
        $rest = $withShare->slice(8);

        if ($rest->isNotEmpty()) {
            $top->push([
                'name' => 'Other departments',
                'color' => 'slate',
                'items' => (int) $rest->sum('items'),
                'value' => (float) $rest->sum('value'),
                'share' => round((float) $rest->sum('share'), 1),
                'isOther' => true,
            ]);
        }

        return $top->values();
    }

    /**
     * @return array{items: Collection<int, array{product: Product, total: int}>, total: int}
     */
    private function topMovers(): array
    {
        $from = now()->subDays(self::CHART_DAYS)->startOfDay();

        $totals = StockMovement::query()
            ->where('created_at', '>=', $from)
            ->select('product_id', DB::raw('SUM(ABS(quantity)) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'product_id');

        $items = Product::query()
            ->with('category')
            ->whereIn('id', $totals->keys())
            ->get()
            ->sortByDesc(fn (Product $product) => (int) $totals[$product->id])
            ->take(5)
            ->map(fn (Product $product) => [
                'product' => $product,
                'total' => (int) $totals[$product->id],
            ])
            ->values();

        return ['items' => $items, 'total' => (int) $items->sum('total')];
    }

    private function recentActivity(): Collection
    {
        return ActivityLog::query()
            ->with('user:id,name,role')
            ->where('action', '!=', 'login')
            ->latest()
            ->limit(7)
            ->get();
    }

    private function team(): Collection
    {
        return User::query()
            ->select('id', 'name', 'avatar', 'role', 'last_login_at')
            ->where('is_active', true)
            ->orderByDesc('last_login_at')
            ->limit(4)
            ->get();
    }
}
