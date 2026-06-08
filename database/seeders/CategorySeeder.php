<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed equipment categories used by club admins when creating listings.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Audio & PA Systems', 'icon' => 'fa-volume-high'],
            ['name' => 'Lighting Equipment', 'icon' => 'fa-lightbulb'],
            ['name' => 'Displays & Projection', 'icon' => 'fa-display'],
            ['name' => 'Cameras & Videography', 'icon' => 'fa-camera'],
            ['name' => 'Laptops & Computers', 'icon' => 'fa-laptop'],
            ['name' => 'Networking Equipment', 'icon' => 'fa-network-wired'],
            ['name' => 'Batteries & Power Banks', 'icon' => 'fa-battery-full'],
            ['name' => 'Sports Equipment', 'icon' => 'fa-football'],
            ['name' => 'Outdoor Recreation Gear', 'icon' => 'fa-campground'],
            ['name' => 'Event Supplies & Decorations', 'icon' => 'fa-wand-magic-sparkles'],
            ['name' => 'Furniture & Staging', 'icon' => 'fa-chair'],
            ['name' => 'Storage & Transport (trolleys, boxes)', 'icon' => 'fa-dolly'],
            ['name' => 'Safety Equipment', 'icon' => 'fa-helmet-safety'],
            ['name' => 'First Aid Supplies', 'icon' => 'fa-kit-medical'],
            ['name' => 'Musical Instruments', 'icon' => 'fa-guitar'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => null,
                    'icon' => $category['icon'],
                ]
            );
        }
    }
}
