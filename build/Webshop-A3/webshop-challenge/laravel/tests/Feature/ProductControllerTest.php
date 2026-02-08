<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_movies_page_is_accessible(): void
    {
        Product::factory()->movie()->count(3)->create();

        $response = $this->get(route('movies'));

        $response->assertStatus(200);
        $response->assertViewIs('movies');
        $response->assertViewHas('movies');
    }

    public function test_games_page_is_accessible(): void
    {
        Product::factory()->game()->count(3)->create();

        $response = $this->get(route('games'));

        $response->assertStatus(200);
        $response->assertViewIs('games');
        $response->assertViewHas('games');
    }

    public function test_merch_page_is_accessible(): void
    {
        Product::factory()->merch()->count(3)->create();

        $response = $this->get(route('merch'));

        $response->assertStatus(200);
        $response->assertViewIs('merch');
        $response->assertViewHas('merch');
    }

    public function test_movies_page_filters_by_product_type(): void
    {
        Product::factory()->movie()->create(['name' => 'Friday the 13th']);
        Product::factory()->game()->create(['name' => 'Friday the 13th: The Game']);
        Product::factory()->merch()->create(['name' => 'Jason Mask']);

        $response = $this->get(route('movies'));

        $response->assertStatus(200);
        $movies = $response->viewData('movies');

        $this->assertCount(1, $movies);
        $this->assertEquals('movie', $movies->first()->product_type);
    }

    public function test_movies_filter_api_returns_json(): void
    {
        Product::factory()->movie()->count(3)->create();

        $response = $this->getJson(route('products.filter', ['type' => 'movie']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'products' => [
                '*' => ['product_id', 'name', 'price', 'product_type'],
            ],
            'count',
        ]);
    }

    public function test_filter_api_filters_by_price(): void
    {
        Product::factory()->movie()->create(['price' => 10.00]);
        Product::factory()->movie()->create(['price' => 30.00]);
        Product::factory()->movie()->create(['price' => 50.00]);

        $response = $this->getJson(route('products.filter', [
            'type' => 'movie',
            'min_price' => 20,
            'max_price' => 40,
        ]));

        $response->assertStatus(200);
        $products = $response->json('products');

        $this->assertCount(1, $products);
        $this->assertEquals(30.00, $products[0]['price']);
    }

    public function test_filter_api_sorts_by_price(): void
    {
        Product::factory()->movie()->create(['price' => 30.00]);
        Product::factory()->movie()->create(['price' => 10.00]);
        Product::factory()->movie()->create(['price' => 50.00]);

        $response = $this->getJson(route('products.filter', [
            'type' => 'movie',
            'sort_by' => 'price',
            'sort_order' => 'asc',
        ]));

        $response->assertStatus(200);
        $products = $response->json('products');

        $this->assertEquals(10.00, $products[0]['price']);
        $this->assertEquals(30.00, $products[1]['price']);
        $this->assertEquals(50.00, $products[2]['price']);
    }

    public function test_filter_api_sorts_by_name(): void
    {
        Product::factory()->movie()->create(['name' => 'Friday the 13th Part III']);
        Product::factory()->movie()->create(['name' => 'Friday the 13th Part I']);
        Product::factory()->movie()->create(['name' => 'Friday the 13th Part II']);

        $response = $this->getJson(route('products.filter', [
            'type' => 'movie',
            'sort_by' => 'name',
            'sort_order' => 'asc',
        ]));

        $response->assertStatus(200);
        $products = $response->json('products');

        $this->assertEquals('Friday the 13th Part I', $products[0]['name']);
        $this->assertEquals('Friday the 13th Part II', $products[1]['name']);
        $this->assertEquals('Friday the 13th Part III', $products[2]['name']);
    }

    public function test_filter_api_returns_error_for_invalid_type(): void
    {
        $response = $this->getJson(route('products.filter', ['type' => 'invalid']));

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid product type']);
    }

    public function test_games_filter_api_returns_only_games(): void
    {
        Product::factory()->movie()->create();
        Product::factory()->game()->create();
        Product::factory()->merch()->create();

        $response = $this->getJson(route('products.filter', ['type' => 'game']));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('count'));
        $this->assertEquals('game', $response->json('products.0.product_type'));
    }

    public function test_merch_filter_api_returns_only_merch(): void
    {
        Product::factory()->movie()->create();
        Product::factory()->game()->create();
        Product::factory()->merch()->create();

        $response = $this->getJson(route('products.filter', ['type' => 'merch']));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('count'));
        $this->assertEquals('merch', $response->json('products.0.product_type'));
    }
}
