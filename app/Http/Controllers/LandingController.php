<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\TeamAlertService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The page each role lands on after signing in.
 *
 * They are separate pages rather than one dashboard with everything hidden,
 * so an administrator, a manager, a member of staff and a customer each see
 * the work that is actually theirs. The role check is here on the server: a
 * route name is not a permission.
 */
class LandingController extends Controller
{
    /** The manager's own page: the shop at a glance, without user administration. */
    public function manager(Request $request): View
    {
        $this->guard($request, Role::Manager);

        return view('admin.manager', [
            'stats' => [
                'orders' => Order::query()->count(),
                'awaitingPayment' => Order::query()->where('status', OrderStatus::Pending)->count(),
                'toComplete' => Order::query()->where('status', OrderStatus::Paid)->count(),
                'lowStock' => Product::query()->lowStock()->count(),
                'pendingAccounts' => User::query()->pending()->count(),
                'openAlerts' => app(TeamAlertService::class)->unreadCount(),
            ],
            'recentOrders' => Order::query()->latest('placed_at')->limit(6)->get(),
            // The same waiting people, listed here so the decision can be made
            // without opening the queue.
            'waitingAccounts' => User::query()
                ->pending()
                ->orderBy('created_at')
                ->limit(5)
                ->get(),
        ]);
    }

    /** The staff page: goods and stock, which is all they can act on. */
    public function staff(Request $request): View
    {
        $this->guard($request, Role::Staff);

        return view('admin.staff', [
            'stats' => [
                'goods' => Product::query()->count(),
                'lowStock' => Product::query()->lowStock()->count(),
                'outOfStock' => Product::query()->where('stock', '<=', 0)->count(),
                'ordersToPick' => Order::query()->where('status', OrderStatus::Paid)->count(),
            ],
            'lowStock' => Product::query()->lowStock()->orderBy('stock')->limit(8)->get(),
        ]);
    }

    /** The customer page. Ordering never needs an account; this is for looking. */
    public function account(Request $request): View
    {
        $user = $request->user();

        $this->guard($request, Role::Customer);

        // Orders are not tied to an account, because anybody can order as a
        // guest, so a customer sees the ones placed with their email address.
        return view('account.index', [
            'user' => $user,
            'orders' => Order::query()
                ->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $user->email)])
                ->latest('placed_at')
                ->limit(20)
                ->get(),
        ]);
    }

    /**
     * Refuse anybody who is not the role this page belongs to.
     */
    private function guard(Request $request, Role $role): void
    {
        abort_unless($request->user()?->role === $role, 403);
    }
}
