<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Notifications\TeamAlertService;
use App\Support\Delivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user:id,username,name')
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%'.$request->string('q')->toString().'%';
                $q->where(function ($sub) use ($like) {
                    $sub->where('order_number', 'like', $like)
                        ->orWhere('customer_name', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('gateway'), fn ($q) => $q->where('payment_gateway', $request->string('gateway')->toString()))
            ->latest('placed_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Order::query()->count(),
            'pending' => Order::query()->where('status', OrderStatus::Pending)->count(),
            'paid' => Order::query()->where('status', OrderStatus::Paid)->count(),
            'completed' => Order::query()->where('status', OrderStatus::Completed)->count(),
            'revenue' => (int) Order::query()->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed])->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(
                'items.product:id,name,slug,produced_at,expires_at,available_from',
                'payments',
                'user:id,username,name,phone',
            ),
        ]);
    }

    public function edit(Order $order): View
    {
        return view('admin.orders.edit', [
            'order' => $order,
            'townships' => Delivery::townships(),
        ]);
    }

    /**
     * Correct the delivery details on an order.
     *
     * Only who and where: the lines and the total are what the shopper agreed
     * to and what was charged, so they are not editable here. A closed order
     * keeps the record it has.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        if ($order->status->isClosed()) {
            return back()->with('error', 'A '.$order->status->label().' order cannot be edited.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'township' => ['required', 'string', Rule::in(Delivery::townships())],
        ]);

        $order->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'description' => 'Edited the delivery details on '.$order->order_number,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order '.$order->order_number.' was updated.');
    }

    /**
     * Move an order along. The status enum decides what is allowed, so a closed
     * order cannot be reopened from a stale menu.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_column(OrderStatus::cases(), 'value'))],
        ]);

        $next = OrderStatus::from($data['status']);

        if (! $order->status->canTransitionTo($next)) {
            return back()->with(
                'error',
                'An order that is '.$order->status->label().' cannot become '.$next->label().'.'
            );
        }

        // A paid order becomes completed, not refunded, and vice versa; a refund
        // is settled with the provider, so nothing moves here.
        $order->forceFill(['status' => $next])->save();

        if ($next === OrderStatus::Cancelled) {
            $order->payments()->where('status', 'pending')->update(['status' => 'failed']);
        }

        // An order that has been completed, cancelled or refunded has been dealt
        // with, so its bell entry goes rather than lingering as stale work.
        if ($next->isClosed()) {
            app(TeamAlertService::class)->clearForOrder($order->id);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'description' => $order->order_number.' marked '.$next->label(),
        ]);

        return back()->with('success', 'Order '.$order->order_number.' is now '.$next->label().'.');
    }

    /**
     * Remove an order outright.
     *
     * Only one that has not been paid for: a paid order has stock movements and
     * a payment behind it, and the record of it is the point of keeping it.
     */
    public function destroy(Request $request, Order $order): RedirectResponse
    {
        if ($order->isPaid() || $order->status === OrderStatus::Completed) {
            return back()->with(
                'error',
                'A paid order cannot be deleted. Cancel it while it is unpaid, or refund it once it is paid.'
            );
        }

        $number = $order->order_number;

        // The alert pointed at this order, so it goes with it rather than being
        // left behind as a link to a page that no longer exists.
        app(TeamAlertService::class)->clearForOrder($order->id);

        $order->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deleted',
            'subject_type' => Order::class,
            'description' => 'Deleted order '.$number,
        ]);

        return back()->with('success', 'Order '.$number.' was deleted.');
    }
}
