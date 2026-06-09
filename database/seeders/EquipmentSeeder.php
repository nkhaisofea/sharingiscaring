<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $club = User::where('role', 'club_admin')
            ->whereNotNull('club_name')
            ->first();

        if (!$club) {
            return;
        }

        $items = [
            [
                'category' => 'Audio & PA Systems',
                'name' => 'Portable PA Speaker Set',
                'description' => 'Two powered speakers with stands and basic cables for small events.',
                'price_per_day' => 45,
                'condition' => 'good',
                'pickup_location' => 'IIUM Sports Club Office',
            ],
            [
                'category' => 'Cameras & Videography',
                'name' => 'Canon DSLR Event Kit',
                'description' => 'DSLR body, kit lens, battery, charger, and carry bag.',
                'price_per_day' => 60,
                'condition' => 'excellent',
                'pickup_location' => 'Student Activity Centre',
            ],
            [
                'category' => 'Outdoor Recreation Gear',
                'name' => 'Four-Person Camping Tent',
                'description' => 'Water-resistant dome tent suitable for outdoor club programs.',
                'price_per_day' => 25,
                'condition' => 'good',
                'pickup_location' => 'IIUM Sports Complex',
            ],
            [
                'category' => 'Displays & Projection',
                'name' => 'HD Projector With Screen',
                'description' => 'Portable projector and foldable screen for meetings or talks.',
                'price_per_day' => 50,
                'condition' => 'excellent',
                'pickup_location' => 'Kulliyyah of ICT Lobby',
            ],
            [
                'category' => 'Event Supplies & Decorations',
                'name' => 'Event Registration Table Kit',
                'description' => 'Foldable table, tablecloth, queue signage, and stationery box.',
                'price_per_day' => 15,
                'condition' => 'fair',
                'pickup_location' => 'Main Hall Store',
            ],
        ];

        foreach ($items as $item) {
            $category = Category::where('name', $item['category'])->first();

            if (!$category) {
                continue;
            }

            Equipment::updateOrCreate(
                [
                    'club_id' => $club->id,
                    'name' => $item['name'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $item['description'],
                    'price_per_day' => $item['price_per_day'],
                    'condition' => $item['condition'],
                    'availability_status' => 'available',
                    'pickup_location' => $item['pickup_location'],
                ]
            );
        }
    }
}
