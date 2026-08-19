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
        // "/" requires an admin session, so a guest is redirected to the login page.
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
