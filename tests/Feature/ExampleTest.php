<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_import_and_settings_pages_render(): void
    {
        $this->get('/imports/create')->assertStatus(200);
        $this->get('/settings')->assertStatus(200);
        $this->get('/references')->assertNotFound();
    }
}
