<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'jason_voorhees',
            'email' => 'jason@campcrystallake.com',
            'password' => 'friday13th',
            'password_confirmation' => 'friday13th',
            'terms' => true,
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'username' => 'jason_voorhees',
            'email' => 'jason@campcrystallake.com',
        ]);
    }

    public function test_register_requires_unique_username(): void
    {
        User::factory()->create(['username' => 'jason_voorhees']);

        $response = $this->post(route('register'), [
            'username' => 'jason_voorhees',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_register_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'jason@campcrystallake.com']);

        $response = $this->post(route('register'), [
            'username' => 'new_user',
            'email' => 'jason@campcrystallake.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'jason_voorhees',
            'email' => 'jason@campcrystallake.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'jason@campcrystallake.com',
            'password' => Hash::make('friday13th'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jason@campcrystallake.com',
            'password' => 'friday13th',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'jason@campcrystallake.com',
            'password' => Hash::make('friday13th'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jason@campcrystallake.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('home'));
    }

    public function test_authenticated_user_is_redirected_from_register(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('home'));
    }
}
