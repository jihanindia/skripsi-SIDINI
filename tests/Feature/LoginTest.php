<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * Test that the login page displays the correct email input field.
     */
    public function test_login_page_renders_email_field(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('name="email"', false);
        $response->assertSee('placeholder="Masukkan email"', false);
        $response->assertDontSee('name="login"', false);
    }

    /**
     * Test login succeeds with correct credentials.
     */
    public function test_login_succeeds_with_correct_credentials(): void
    {
        $guard = \Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('check')
            ->andReturn(false);

        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'test@example.com', 'password' => 'password123'], null)
            ->andReturn(true);

        Auth::shouldReceive('guard')
            ->andReturn($guard);

        // Since session is used in the controller, session gets simulated
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test login fails with incorrect credentials and displays custom message.
     */
    public function test_login_fails_with_incorrect_credentials(): void
    {
        $guard = \Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('check')
            ->andReturn(false);

        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'test@example.com', 'password' => 'wrongpassword'], null)
            ->andReturn(false);

        Auth::shouldReceive('guard')
            ->andReturn($guard);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Email dan password salah. Silahkan masukkan kembali'
        ]);
        $this->assertGuest();
    }
}
