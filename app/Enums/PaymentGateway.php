<?php

namespace App\Enums;

use App\Payments\Gateways\AyaPayGateway;
use App\Payments\Gateways\KbzPayGateway;
use App\Payments\Gateways\SandboxGateway;
use App\Payments\Gateways\UabPayGateway;
use App\Payments\Gateways\WaveMoneyGateway;

/**
 * Online banking / wallet options offered at checkout.
 *
 * Each case carries what the UI needs (label, blurb, brand colour, whether the
 * provider can render a scan-to-pay QR code) and maps to a gateway class in
 * App\Payments\Gateways.
 */
enum PaymentGateway: string
{
    case KbzPay = 'kbzpay';
    case WaveMoney = 'wavemoney';
    case AyaPay = 'ayapay';
    case UabPay = 'uabpay';
    case Cash = 'cash';
    case Sandbox = 'sandbox';

    public function label(): string
    {
        return match ($this) {
            self::KbzPay => 'KBZPay',
            self::WaveMoney => 'Wave Money',
            self::AyaPay => 'AyaPay',
            self::UabPay => 'UAB Pay',
            self::Cash => 'Cash on delivery',
            self::Sandbox => 'Sandbox (test)',
        };
    }

    public function blurb(): string
    {
        return match ($this) {
            self::KbzPay => 'Pay with the KBZPay app or scan with KBZ DirectPay.',
            self::WaveMoney => 'Pay from your Wave Money wallet.',
            self::AyaPay => 'Pay from your AyaPay wallet.',
            self::UabPay => 'Pay with a UAB card or uabpay+ app.',
            self::Cash => 'Pay the rider in cash when your goods arrive.',
            self::Sandbox => 'Simulated gateway for local testing — no real money moves.',
        };
    }

    public function gatewayClass(): ?string
    {
        return match ($this) {
            self::KbzPay => KbzPayGateway::class,
            self::WaveMoney => WaveMoneyGateway::class,
            self::AyaPay => AyaPayGateway::class,
            self::UabPay => UabPayGateway::class,
            self::Sandbox => SandboxGateway::class,
            self::Cash => null,
        };
    }

    public function supportsQr(): bool
    {
        return in_array($this, [self::KbzPay, self::WaveMoney, self::AyaPay, self::UabPay], true);
    }

    public function requiresOnline(): bool
    {
        return $this !== self::Cash;
    }

    public function tone(): string
    {
        return match ($this) {
            self::KbzPay => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::WaveMoney => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            self::AyaPay => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            self::UabPay => 'bg-sky-50 text-sky-700 ring-sky-600/20',
            self::Cash => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Sandbox => 'bg-ink-100 text-ink-600 ring-ink-500/10',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
