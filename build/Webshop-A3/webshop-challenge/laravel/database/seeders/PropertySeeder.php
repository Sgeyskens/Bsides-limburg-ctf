<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = [
            // Movie properties
            ['property_name' => 'Genre', 'property_type' => 'string'],
            ['property_name' => 'Year', 'property_type' => 'integer'],
            ['property_name' => 'Director', 'property_type' => 'string'],
            ['property_name' => 'Runtime', 'property_type' => 'string'],
            
            // Game properties
            ['property_name' => 'Platform', 'property_type' => 'string'],
            ['property_name' => 'Publisher', 'property_type' => 'string'],
            ['property_name' => 'ESRB Rating', 'property_type' => 'string'],
            
            // Merch properties
            ['property_name' => 'Size', 'property_type' => 'string'],
            ['property_name' => 'Color', 'property_type' => 'string'],
            ['property_name' => 'Material', 'property_type' => 'string'],
        ];

        foreach ($properties as $property) {
            DB::table('property')->updateOrInsert(
                ['property_name' => $property['property_name']], // Unique key
                [
                    'property_type' => $property['property_type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Properties seeded successfully!');
    }
}