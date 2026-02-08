<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'username' => 'jason_voorhees',
            'email' => 'jason@campcrystallake.com',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'jason_voorhees',
            'email' => 'jason@campcrystallake.com',
        ]);
    }

    public function test_user_is_not_admin_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
    }

    public function test_admin_factory_state(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->isAdmin());
    }

    public function test_user_has_one_cart(): void
    {
        $user = User::factory()->create();
        Cart::factory()->create(['user_id' => $user->user_id]);

        $this->assertNotNull($user->cart);
        $this->assertInstanceOf(Cart::class, $user->cart);
    }

    public function test_user_has_many_orders(): void
    {
        $user = User::factory()->create();

        Order::factory()->count(3)->create(['user_id' => $user->user_id]);

        $this->assertCount(3, $user->orders);
    }

    public function test_avatar_url_returns_default_when_null(): void
    {
        $user = User::factory()->create(['avatar_url' => null]);

        $this->assertStringContainsString('mask-logo.png', $user->avatar_url);
    }

    public function test_avatar_url_returns_value_when_set(): void
    {
        $user = User::factory()->create(['avatar_url' => 'http://example.com/avatar.jpg']);

        $this->assertEquals('http://example.com/avatar.jpg', $user->getRawOriginal('avatar_url'));
    }

    public function test_password_is_hidden(): void
    {
        $user = User::factory()->create();

        $this->assertArrayNotHasKey('password', $user->toArray());
    }
}
