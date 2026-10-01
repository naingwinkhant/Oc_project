<?php

namespace App\Http\Controllers;

use App\Cart\CartService;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\TeamAlertService;
use App\Payments\PaymentManager;
use App\Support\Delivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PaymentManager $payments,
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($this->cart->hasBlockedItems()) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart can no longer be sold. Please remove them first.');
        }

        return view('checkout.create', [
            'items' => $this->cart->items(),
            'summary' => $this->cart->summary(),
            'amountUntilFree' => $this->cart->amountUntilFreeDelivery(),
            'gateways' => $this->payments->available(),
            'townships' => Delivery::townships(),
            'townshipMap' => Delivery::townshipMap($this->cart->subtotal()),
            'zones' => Delivery::zones(),
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($this->cart->hasBlockedItems()) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart can no longer be sold. Please remove them first.');
        }

        $gateway = PaymentGateway::tryFrom((string) $request->input('payment_gateway'));

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^0?9[0-9\s-]{7,13}$/'],
            'email' => ['required', 'email', 'max:190'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'township' => ['required', 'string', 'max:80', Rule::in(Delivery::townships())],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_gateway' => ['required', Rule::in(PaymentGateway::values())],
        ], [
            'phone.regex' => 'Enter a Myanmar mobile number, for example 09 380 000 00.',
            'township.in' => 'We do not deliver to that township yet.',
        ]);

        if (! $gateway || ! $this->payments->isAvailable($gateway)) {
            return back()
                ->withInput()
                ->withErrors(['payment_gateway' => 'That payment method is not available right now.']);
        }

        $order = DB::transaction(function () use ($data, $gateway) {
            $subtotal = $this->cart->subtotal();
            $delivery = Delivery::feeFor($data['township'], $subtotal);
            $zone = Delivery::zoneFor($data['township']);

            $order = Order::create([
                ...$data,
                'order_number' => Order::generateNumber(),
                'user_id' => auth()->id(),
                'delivery_zone' => $zone['key'] ?? null,
                'delivery_eta' => $zone['eta'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $delivery,
                'total' => $subtotal + $delivery,
                'status' => OrderStatus::Pending,
                'payment_gateway' => $gateway,
                'placed_at' => now(),
            ]);

            foreach ($this->cart->items() as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'sku' => $item['product']->sku,
                    'unit' => $item['product']->unit,
                    'unit_price' => $item['product']->effectivePrice(),
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order;
        });

        $this->cart->clear();

        // Somebody is on the till, so tell them a basket has just been placed.
        // The shopper is never shown this: team alerts are behind sign-in.
        app(TeamAlertService::class)->orderPlaced($order);

        return redirect()->route('checkout.show', $order);
    }

    public function show(Order $order): View|RedirectResponse
    {
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->isPaid() || $order->status === OrderStatus::Cancelled) {
            return view('checkout.show', [
                'order' => $order->load('items.product', 'latestPayment'),
                'intent' => null,
            ]);
        }

        $gateway = $order->payment_gateway ?? PaymentGateway::Cash;
        $intent = null;

        if ($gateway->requiresOnline()) {
            try {
                $payment = $this->pendingPaymentFor($order, $gateway);

                // Only ask the provider for a new intent when we do not already
                // hold an open one, so a page reload cannot mint duplicates.
                if (! $payment) {
                    $result = $this->payments->resolve($gateway)->createPayment($order, [
                        'name' => $order->customer_name,
                        'phone' => $order->phone,
                        'email' => $order->email,
                    ]);

                    $payment = $order->payments()->create([
                        'gateway' => $gateway,
                        'gateway_reference' => $result['reference'],
                        'amount' => $order->total,
                        'status' => PaymentStatus::Pending,
                        'payload' => [
                            ...($result['payload'] ?? []),
                            'redirect_url' => $result['redirect_url'] ?? null,
                            'qr_payload' => $result['qr_payload'] ?? null,
                        ],
                    ]);

                    $order->forceFill(['payment_reference' => $result['reference']])->save();

                    $intent = $result;
                } else {
                    $intent = [
                        'reference' => $payment->gateway_reference,
                        'redirect_url' => $payment->redirectUrl(),
                        'qr_payload' => $payment->payload['qr_payload'] ?? null,
                        'payload' => $payment->payload,
                    ];
                }
            } catch (\Throwable $e) {
                report($e);

                return view('checkout.show', [
                    'order' => $order->load('items'),
                    'intent' => null,
                    'gatewayError' => 'We could not reach '.strtolower($gateway->label()).'. You can retry the payment or choose cash on delivery.',
                ]);
            }
        }

        return view('checkout.show', [
            'order' => $order->load('items.product', 'latestPayment'),
            'intent' => $intent,
        ]);
    }

    /**
     * The still-open payment attempt for this order, if the customer has one.
     */
    private function pendingPaymentFor(Order $order, PaymentGateway $gateway): ?Payment
    {
        return $order->payments()
            ->where('gateway', $gateway)
            ->where('status', PaymentStatus::Pending)
            ->latest('id')
            ->first();
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($order->isPaid()) {
            return back();
        }

        $order->forceFill(['status' => OrderStatus::Cancelled])->save();
        $order->payments()->update(['status' => PaymentStatus::Failed]);

        return redirect()
            ->route('catalog.index')
            ->with('status', 'Order '.$order->order_number.' was cancelled.');
    }
}
