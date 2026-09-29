<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_the_public_catalogue(): void
    {
        $this->get('/')->assertRedirect(route('catalog.index'));
    }

    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_login_screen_renders(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign in');
    }
}
