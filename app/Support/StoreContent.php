<?php

namespace App\Support;

use App\Enums\PaymentGateway;

/**
 * The words the store puts in front of customers.
 *
 * The delivery fees, the payment list and the expiry policy all read from
 * config, so a page can never promise something the checkout does not do. The
 * Settings page and the Services page both render these sections rather than
 * keeping their own copies.
 */
class StoreContent
{
    /**
     * The rules the store actually enforces, grouped for display.
     *
     * @return array<int, array{title: string, lines: array<int, string>}>
     */
    public static function rules(): array
    {
        $delivery = config('shop.delivery');
        $symbol = Money::symbol();

        $zones = collect($delivery['zones'])->map(fn (array $zone) => sprintf(
            '%s: %s%s — %s.',
            $zone['label'],
            $symbol,
            number_format($zone['fee']),
            $zone['eta']
        ))->all();

        return [
            [
                'title' => 'Freshness and what we will not sell',
                'lines' => [
                    'Every item carries a production date and an expiry date, both shown on its page.',
                    'A batch is withdrawn from sale the day after it expires — the cart and the checkout will refuse it.',
                    'A batch that has been announced but has not landed yet is listed as coming soon and cannot be bought early.',
                    'Anything close to its expiry date is marked, so you can buy it deliberately rather than find it gone.',
                ],
            ],
            [
                'title' => 'How many you may buy',
                'lines' => [
                    'The amount you can add is capped by what is on the shelf, never above it.',
                    'If the shelf is nearly empty when you check out, the order is held until you reduce the amount.',
                ],
            ],
            [
                'title' => 'Delivery',
                'lines' => array_merge($zones, [
                    'Free over '.$symbol.number_format($delivery['free_over']).'.',
                    'The fee is worked out from the township you choose at checkout, and shown before you pay.',
                ]),
            ],
            [
                'title' => 'Payment',
                'lines' => [
                    self::paymentSentence(),
                    'Stock only moves once the payment is confirmed, so an unpaid basket never takes goods off the shelf.',
                ],
            ],
            [
                'title' => 'Cancelling and changes',
                'lines' => [
                    'You can cancel from the order screen until the payment goes through.',
                    'Once paid, call the store with your order number to arrange a change or a return.',
                ],
            ],
        ];
    }

    /**
     * What the store does for the customer, as cards.
     *
     * @return array<int, array{icon: string, title: string, body: string}>
     */
    public static function services(): array
    {
        $delivery = config('shop.delivery');
        $symbol = Money::symbol();

        $cheapest = collect($delivery['zones'])->min('fee');
        $dearest = collect($delivery['zones'])->max('fee');
        $free = $symbol.number_format($delivery['free_over']);
        $inner = collect($delivery['zones'])->firstWhere('key', 'inner');

        $services = [
            [
                'icon' => 'truck',
                'title' => 'Zoned delivery',
                'body' => sprintf(
                    'Order before 2pm and your goods reach the inner townships the same day. Across Yangon it is %s%s, and further out %s%s. Nothing to pay on orders over %s.',
                    $symbol,
                    number_format($cheapest),
                    $symbol,
                    number_format($dearest),
                    $free
                ),
            ],
            [
                'icon' => 'tag-check',
                'title' => 'Pay how you like',
                'body' => self::paymentSentence().' No account is needed to order.',
            ],
            [
                'icon' => 'clock',
                'title' => 'Freshness you can check',
                'body' => 'Production and expiry dates sit on every product page, and anything close to its date is flagged before you buy rather than after.',
            ],
            [
                'icon' => 'refresh',
                'title' => 'Cancel or change',
                'body' => 'Cancel from the order screen until the payment goes through. After that, call the store with your order number and we will sort it out.',
            ],
            [
                'icon' => 'clipboard',
                'title' => 'A real basket, not a guess',
                'body' => 'Promotions are the price you pay, the order summary shows every line, and stock is capped at what is actually on the shelf.',
            ],
            [
                'icon' => 'heart',
                'title' => 'Saved for later',
                'body' => 'Favourites and recently viewed are kept on this device, and follow your account if you sign in.',
            ],
        ];

        if ($inner) {
            $services[] = [
                'icon' => 'store',
                'title' => 'Walk in any time',
                'body' => 'The '.$inner['label'].' store is open daily, and the hours are on the Information page.',
            ];
        }

        return $services;
    }

    /**
     * The store's own details, for the Information page.
     *
     * @return array<string, mixed>
     */
    public static function information(): array
    {
        return [
            'about' => (array) config('shop.info.about', []),
            'hours' => (array) config('shop.info.hours', []),
            'branches' => (array) config('shop.info.branches', []),
            'phone' => config('shop.phone'),
            'email' => config('shop.email'),
            'address' => config('shop.address'),
            'founded' => config('shop.founded'),
            'paymentNote' => config('shop.info.payment_note'),
        ];
    }

    /**
     * "Pay with KBZPay, Wave Money, AyaPay or Cash on delivery."
     */
    private static function paymentSentence(): string
    {
        $gateways = collect(PaymentGateway::cases())
            ->reject(fn (PaymentGateway $gateway) => $gateway === PaymentGateway::Sandbox)
            ->map(fn (PaymentGateway $gateway) => $gateway->label())
            ->values();

        $last = $gateways->pop();

        return 'Pay with '.ucfirst($gateways->implode(', ')).($gateways->isEmpty() ? '' : ' or ').$last.'.';
    }
}
