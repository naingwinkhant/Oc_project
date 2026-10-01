<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\StoreContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_is_open_to_everyone_without_an_account(): void
    {
        $this->get(route('services'))
            ->assertOk()
            ->assertSee('Services')
            ->assertSee('Zoned delivery')
            ->assertSee('The rules we sell by');
    }

    public function test_information_is_open_to_everyone_without_an_account(): void
    {
        $this->get(route('information'))
            ->assertOk()
            ->assertSee('Information')
            ->assertSee('Opening hours')
            ->assertSee(config('shop.phone'))
            ->assertSee(config('shop.email'));
    }

    public function test_both_pages_are_reached_from_settings_rather_than_the_nav_bar(): void
    {
        $settings = $this->get(route('settings'))->assertOk();

        $settings->assertSee(route('services'))
            ->assertSee(route('information'))
            ->assertSee('Delivery, payment, freshness and changes')
            ->assertSee('About us, branches and opening hours');

        // The classifications own the nav bar, so the store pages are not in it.
        $shop = $this->get(route('catalog.index'))->assertOk()->getContent();

        $this->assertStringNotContainsString(route('services'), $shop);
        $this->assertStringNotContainsString(route('information'), $shop);
    }

    public function test_the_store_pages_are_open_to_a_signed_in_member_too(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('services'))->assertOk()->assertSee('Services');
        $this->actingAs($staff)->get(route('information'))->assertOk()->assertSee('Opening hours');
    }

    public function test_the_service_page_quotes_the_delivery_fees_the_checkout_uses(): void
    {
        $delivery = config('shop.delivery');
        $html = $this->get(route('services'))->assertOk()->getContent();

        foreach ($delivery['zones'] as $zone) {
            $this->assertStringContainsString($zone['label'], $html);
            $this->assertStringContainsString(number_format($zone['fee']), $html);
        }

        $this->assertStringContainsString(number_format($delivery['free_over']), $html);
    }

    public function test_the_service_page_names_every_gateway_a_shopper_can_pay_with(): void
    {
        $html = $this->get(route('services'))->assertOk()->getContent();

        foreach (['KBZPay', 'Wave Money', 'AyaPay', 'UAB Pay', 'Cash on delivery'] as $gateway) {
            $this->assertStringContainsString($gateway, $html);
        }

        // The test driver is never advertised to a shopper.
        $this->assertStringNotContainsString('Sandbox', $html);
    }

    public function test_the_payment_sentence_is_not_repeated(): void
    {
        $payment = collect(StoreContent::rules())->firstWhere('title', 'Payment');

        $this->assertNotNull($payment);
        $this->assertSame(
            1,
            substr_count($payment['lines'][0], 'Cash on delivery'),
            'Cash on delivery should be named once, not twice in the same sentence.'
        );
    }

    public function test_services_and_settings_render_the_same_rules(): void
    {
        $services = $this->get(route('services'))->assertOk()->getContent();

        foreach (StoreContent::rules() as $rule) {
            $this->assertStringContainsString($rule['title'], $services);

            foreach ($rule['lines'] as $line) {
                $this->assertStringContainsString($line, $services);
            }
        }

        $settings = $this->get(route('settings'))->assertOk()->getContent();

        foreach (StoreContent::rules() as $rule) {
            $this->assertStringContainsString($rule['title'], $settings);
        }
    }

    public function test_a_shopper_can_reach_both_pages_from_the_settings_page(): void
    {
        // Settings is the only place they are linked from, so it has to hold both.
        $html = $this->get(route('settings'))->assertOk()->getContent();

        $this->assertStringContainsString(route('services'), $html);
        $this->assertStringContainsString(route('information'), $html);
    }

    public function test_the_pages_survive_a_store_with_no_branches_configured(): void
    {
        config(['shop.info.branches' => []]);

        $this->get(route('information'))->assertOk()->assertDontSee('Branches');
    }
}
