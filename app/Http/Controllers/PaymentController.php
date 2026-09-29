<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Gateways\SandboxGateway;
use App\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentManager $payments) {}

    /**
     * Server-to-server notification from the provider. Deliberately excluded from
     * CSRF verification because the request is signed by the provider instead.
     */
    public function callback(Request $request, string $gateway): RedirectResponse
    {
        $enum = PaymentGateway::tryFrom($gateway);

        if (! $enum || ! $enum->requiresOnline()) {
            abort(404);
        }

        $reference = $this->payments->resolve($enum)->verifyCallback($request->all());

        if (! $reference) {
            report('Rejected '.$gateway.' callback with an invalid signature.');

            return redirect()
                ->route('catalog.index')
                ->with('error', 'We could not verify that payment. Nothing has been charged.');
        }

        $order = Order::query()
            ->where('order_number', $reference)
            ->orWhere('payment_reference', $reference)
            ->first();

        if (! $order) {
            return redirect()->route('catalog.index')->with('error', 'Unknown order for that payment.');
        }

        $payment = $order->payments()->where('gateway', $enum)->latest()->first();

        if ($payment) {
            $payment->markPaid($reference);
        } else {
            $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();
        }

        return redirect()->route('checkout.show', $order)->with('status', 'Payment received. Thank you!');
    }

    /**
     * Where the provider sends the customer's browser back to.
     */
    public function return(Request $request, string $gateway): RedirectResponse
    {
        $enum = PaymentGateway::tryFrom($gateway);

        if (! $enum) {
            abort(404);
        }

        $reference = (string) $request->input('orderNo', $request->input('order_id', $request->input('reference', '')));

        $order = $reference
            ? Order::query()->where('order_number', $reference)->orWhere('payment_reference', $reference)->first()
            : null;

        if (! $order) {
            return redirect()->route('catalog.index')->with('error', 'We could not find that order.');
        }

        $isFailure = strtolower((string) $request->input('status', '')) === 'failed'
            || $request->boolean('cancel')
            || $request->boolean('error');

        if ($isFailure) {
            $order->payments()->where('gateway', $enum)->update(['status' => PaymentStatus::Failed]);

            return redirect()
                ->route('checkout.show', $order)
                ->with('error', 'The payment was not completed. You can try again.');
        }

        // A return redirect is not proof of payment — the signed callback is.
        // Ask the provider for the authoritative status before settling.
        return redirect()
            ->route('checkout.show', $order)
            ->with('status', 'Almost there — we are confirming your payment.');
    }

    public function sandbox(Order $order): View|RedirectResponse
    {
        if ($order->isPaid()) {
            return redirect()->route('checkout.show', $order);
        }

        return view('checkout.sandbox', [
            'order' => $order->load('items', 'latestPayment'),
            'signature' => hash_hmac('sha256', $order->order_number, (string) config('app.key')),
        ]);
    }

    public function settle(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'signature' => ['required', 'string'],
            'outcome' => ['required', Rule::in(['paid', 'failed'])],
        ]);

        $verified = app(SandboxGateway::class)->verifyCallback([
            'order' => $order->order_number,
            'signature' => $data['signature'],
        ]);

        if (! $verified) {
            return back()->with('error', 'That sandbox payment link has expired. Please retry.');
        }

        $payment = $order->payments()->latest()->first();

        if (! $payment) {
            // The customer can reach the sandbox page straight from the order
            // screen, so make sure there is a row to settle.
            $payment = $order->payments()->create([
                'gateway' => $order->payment_gateway ?? PaymentGateway::Sandbox,
                'gateway_reference' => $order->order_number,
                'amount' => $order->total,
                'status' => PaymentStatus::Pending,
            ]);
        }

        if ($data['outcome'] === 'paid') {
            $payment->markPaid($order->order_number);

            return redirect()
                ->route('checkout.show', $order)
                ->with('status', 'Payment received. Thank you!');
        }

        $payment->markFailed();

        return redirect()
            ->route('checkout.show', $order)
            ->with('error', 'The sandbox payment was declined. Stock was not changed.');
    }
}
