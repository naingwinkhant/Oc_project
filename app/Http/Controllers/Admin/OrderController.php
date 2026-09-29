<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'revenue' => (int) Order::query()->where('status', OrderStatus::Paid)->sum('total'),
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

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->isPaid()) {
            return back()->with('error', 'A paid order cannot change status here. Refund it from the payment provider instead.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,cancelled'],
        ]);

        $order->forceFill([
            'status' => OrderStatus::from($data['status']),
        ])->save();

        if ($data['status'] === 'cancelled') {
            $order->payments()->where('status', 'pending')->update(['status' => 'failed']);
        }

        return back()->with('success', 'Order '.$order->order_number.' updated.');
    }
}
