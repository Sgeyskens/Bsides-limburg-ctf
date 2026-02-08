<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    private const GENRE_SURVIVAL_HORROR = 'Survival Horror';
    private const GENRE_FIGHTING = 'Fighting';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = $this->getMovies();
        $games = $this->getGames();
        $merch = $this->getMerch();

        $allProducts = array_merge(
            $this->addProductType($movies, 'movie'),
            $this->addProductType($games, 'game'),
            $this->addProductType($merch, 'merch')
        );

        foreach ($allProducts as $productData) {
            $this->insertProduct($productData);
        }

        $this->command->info('Products seeded successfully!');
        $this->command->info('Total: ' . count($allProducts) . ' products (8 movies, 8 games, 8 merch)');
    }

    private function addProductType(array $products, string $type): array
    {
        return array_map(fn($product) => array_merge($product, ['product_type' => $type]), $products);
    }

    private function insertProduct(array $productData): void
    {
        $properties = $productData['properties'];
        unset($productData['properties']);

        // Check if product already exists by image_url (unique identifier)
        $existingProduct = DB::table('products')
            ->where('image_url', $productData['image_url'])
            ->first();

        if ($existingProduct) {
            // Product already exists, skip
            return;
        }

        $productId = DB::table('products')->insertGetId([
            'name' => $productData['name'],
            'product_type' => $productData['product_type'],
            'description' => $productData['description'],
            'price' => $productData['price'],
            'image_url' => $productData['image_url'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($properties as $propName => $propValue) {
            $propertyId = DB::table('property')
                ->where('property_name', $propName)
                ->value('property_id');

            if ($propertyId) {
                DB::table('product_property')->insert([
                    'product_id' => $productId,
                    'property_id' => $propertyId,
                    'property_value' => $propValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function getMovies(): array
    {
        return [
            $this->movie('Friday the 13th (1980)', 'The original classic horror film that started it all. Camp Crystal Lake will never be the same.', 'cHeyEAjjfHINNVbemnH8XTLtxQq', '1980'),
            $this->movie('Friday the 13th (Part 2) (1981)', 'Jason returns for more terror at Camp Crystal Lake.', '92rGctBMTv4uaSlIBVnhz01kRWL', '1981'),
            $this->movie('Friday the 13th (Part 3) (1982)', 'Experience the terror in 3D! Jason gets his iconic hockey mask.', 'emc8KNxL7p34YiDkltdWSOo97AQ', '1982'),
            $this->movie('Friday the 13th: The Final Chapter (1984)', 'The final chapter of Jason\'s reign of terror... or is it?', '1J7zudfR3VLPwAf9lK5YPfSu0n6', '1984'),
            $this->movie('Friday the 13th: A New Beginning (1985)', 'Has Jason returned from the grave, or is someone else behind the mask?', 'ewnIs4aCuWnKQ13Eaj8f3ybrQc8', '1985'),
            $this->movie('Friday the 13th Part VI: Jason Lives (1986)', 'Jason returns from the dead to terrorize Camp Crystal Lake once more.', '6vdUpHvkspQonXBdWcLWW5ciEPJ', '1986'),
            $this->movie('Friday the 13th Part VII: The New Blood (1988)', 'Jason faces his most powerful opponent yet - a girl with telekinetic powers.', 'rUzk9Qnyz2FZGxHMBrq6DYIQZkO', '1988'),
            $this->movie('Friday the 13th Part VIII: Jason Takes Manhattan (1989)', 'Jason takes his terror to the big city in this thrilling sequel.', '6ezOsZ9UzqGnbDokaatxdOWP6e9', '1989'),
        ];
    }

    private function movie(string $name, string $description, string $imageId, string $year): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'price' => 14.99,
            'image_url' => "https://image.tmdb.org/t/p/w500/{$imageId}.jpg",
            'properties' => [
                'Genre' => 'Horror/Slasher',
                'Year' => $year,
            ]
        ];
    }

    private function getGames(): array
    {
        return [
            $this->game('Friday the 13th (NES Video Game) (1989)', 'Classic NES game where you must survive Camp Crystal Lake.', 14.99, 'NES-videogame', 'Nintendo NES', '1989', self::GENRE_SURVIVAL_HORROR),
            $this->game('Friday the 13th: The Computer Game (1986)', 'The original computer game adaptation of the horror classic.', 14.99, 'the-computer-game', 'PC', '1986', 'Adventure'),
            $this->game('Friday the 13th: The Game (2017)', 'Modern multiplayer horror game. Play as Jason or survive as a counselor.', 39.99, 'the-game', 'PC/PS4/Xbox One', '2017', self::GENRE_SURVIVAL_HORROR),
            $this->game('Friday the 13th: Killer Puzzle (2018)', 'Puzzle game where you control Jason through challenging levels.', 14.99, 'killer-puzzle', 'Mobile/PC', '2018', 'Puzzle'),
            $this->game('Friday the 13th (2006 Game)', 'Press any key - survive the night at Camp Crystal Lake.', 14.99, '2006-game', 'PC', '2006', 'Horror'),
            $this->game('Terrordrome: Rise of the Boogeymen (2015)', 'Fighting game featuring Jason and other horror icons.', 14.99, 'terrordome', 'PC', '2015', self::GENRE_FIGHTING),
            $this->game('Friday the 13th: Road to Hell (2006)', 'Survival horror adventure through Camp Crystal Lake.', 14.99, 'road-to-hell', 'PC', '2006', self::GENRE_SURVIVAL_HORROR),
            $this->game('MultiVersus (2024)', 'Play as Jason in this crossover fighting game.', 0.00, 'multiverse', 'PC/Console', '2024', self::GENRE_FIGHTING),
        ];
    }

    private function game(string $name, string $description, float $price, string $image, string $platform, string $year, string $genre): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image_url' => "images/products/{$image}.png",
            'properties' => [
                'Platform' => $platform,
                'Year' => $year,
                'Genre' => $genre,
            ]
        ];
    }

    private function getMerch(): array
    {
        return [
            $this->merch('Friday the 13th T-Shirt', 'Official Friday the 13th black graphic t-shirt made from soft, breathable cotton.', 24.99, 't-shirt-black', ['Material' => '100% Cotton', 'Size' => 'S, M, L, XL, XXL', 'Color' => 'Black']),
            $this->merch('Friday the 13th T-Shirt', 'Official Friday the 13th blue graphic t-shirt featuring the same classic horror design.', 24.99, 't-shirt-blue', ['Material' => '100% Cotton', 'Size' => 'S, M, L, XL, XXL', 'Color' => 'Blue']),
            $this->merch('Friday the 13th Backpack', 'Friday the 13th themed backpack designed for everyday use with durable construction.', 39.99, 'backpack', ['Material' => 'Polyester', 'Size' => 'Standard backpack size']),
            $this->merch('Friday the 13th: Funko', 'Collectible Friday the 13th Funko vinyl figure featuring Jason Voorhees.', 29.99, 'funko', ['Material' => 'Vinyl', 'Size' => 'Approximately 4 inches tall']),
            $this->merch('Friday the 13th: Phunny 8" Plush', 'Soft Phunny plush figure of Jason Voorhees, standing approximately 8 inches tall.', 19.99, 'plush', ['Material' => 'Polyester', 'Size' => '8 inches']),
            $this->merch('Friday the 13th: Blacklight crew socks', 'Friday the 13th crew socks featuring blacklight-reactive horror artwork.', 14.99, 'socks', ['Material' => 'Cotton blend', 'Size' => 'One size fits most']),
            $this->merch('Friday the 13th: Mask 3D Resin keychain', '3D resin keychain featuring Jason Voorhees\' iconic hockey mask.', 12.99, 'keychain', ['Material' => 'Resin', 'Size' => 'Keychain size']),
            $this->merch('Friday the 13th: The final chapter 1/4 scale action figure', 'Highly detailed 1/4 scale Jason Voorhees action figure from The Final Chapter.', 129.99, 'action-figure', ['Material' => 'Plastic', 'Size' => '1/4 scale']),
        ];
    }

    private function merch(string $name, string $description, float $price, string $image, array $properties): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image_url' => "images/products/{$image}.png",
            'properties' => $properties,
        ];
    }
}