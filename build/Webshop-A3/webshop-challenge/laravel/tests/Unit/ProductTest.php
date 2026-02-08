<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created(): void
    {
        $product = Product::factory()->create([
            'name' => 'Friday the 13th',
            'price' => 19.99,
            'product_type' => 'movie',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Friday the 13th',
            'price' => 19.99,
            'product_type' => 'movie',
        ]);
    }

    public function test_product_price_is_cast_to_decimal(): void
    {
        $product = Product::factory()->create(['price' => 29.99]);

        $this->assertIsString($product->price);
        $this->assertEquals('29.99', $product->price);
    }

    public function test_movie_factory_state(): void
    {
        $product = Product::factory()->movie()->create();

        $this->assertEquals('movie', $product->product_type);
        $this->assertStringContainsString('Friday the 13th', $product->name);
    }

    public function test_game_factory_state(): void
    {
        $product = Product::factory()->game()->create();

        $this->assertEquals('game', $product->product_type);
    }

    public function test_merch_factory_state(): void
    {
        $product = Product::factory()->merch()->create();

        $this->assertEquals('merch', $product->product_type);
    }

    public function test_tshirt_factory_state(): void
    {
        $product = Product::factory()->tshirt()->create();

        $this->assertEquals('merch', $product->product_type);
        $this->assertStringContainsString('T-Shirt', $product->name);
    }
}
