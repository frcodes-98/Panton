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

    public function test_only_admin_can_sign_in(): void
    {
        $this->post('/login', [
            'username' => 'a.tan',
            'password' => 'demo',
        ])->assertRedirect('/')
            ->assertSessionHasErrors('username')
            ->assertSessionMissing('demo_user');

        $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ])->assertRedirect('/desktop');

        $this->get('/desktop')->assertOk();
    }
}
