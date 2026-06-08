<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_equipment_show_page_as_guest(): void
    {
        // Create category
        $category = Category::create([
            'name' => 'AV Equipment',
            'slug' => 'av-equipment',
            'icon' => 'fa-microphone-alt'
        ]);

        // Create club admin user
        $club = User::forceCreate([
            'name' => 'Musaab Club',
            'email' => 'musaab@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Robotics Club',
            'student_id' => '1234567'
        ]);

        // Create equipment
        $equipment = Equipment::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'Sony Alpha 7 III Camera',
            'description' => 'Excellent camera for sports photography and videography.',
            'price_per_day' => 50.00,
            'condition' => 'excellent',
            'availability_status' => 'available',
            'pickup_location' => 'Male Hostel Block A'
        ]);

        $response = $this->get("/equipment/{$equipment->id}");

        $response->assertStatus(200);
        $response->assertSee('Sony Alpha 7 III Camera');
        $response->assertSee('Excellent camera for sports photography');
        $response->assertSee('RM 50.00');
        $response->assertSee('Excellent Condition');
        $response->assertSee('Male Hostel Block A');
        $response->assertSee('IIUM Robotics Club');
        $response->assertSee('Login to Rent');
    }

    public function test_can_view_equipment_show_page_as_authenticated_borrower(): void
    {
        // Create category
        $category = Category::create([
            'name' => 'Tents',
            'slug' => 'tents',
            'icon' => 'fa-campground'
        ]);

        // Create club admin user
        $club = User::forceCreate([
            'name' => 'Sports Club Admin',
            'email' => 'sports@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Sports Club',
            'student_id' => '1112223'
        ]);

        // Create borrower user
        $borrower = User::forceCreate([
            'name' => 'Student Borrower',
            'email' => 'borrower@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'student_id' => '7654321'
        ]);

        // Create equipment
        $equipment = Equipment::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => '4-Person Camping Tent',
            'description' => 'Waterproof dome camping tent.',
            'price_per_day' => 15.00,
            'condition' => 'good',
            'availability_status' => 'available',
            'pickup_location' => 'IIUM Sports Center'
        ]);

        $response = $this->actingAs($borrower)
                         ->get("/equipment/{$equipment->id}");

        $response->assertStatus(200);
        $response->assertSee('4-Person Camping Tent');
        $response->assertSee('Rent This Equipment');
        $response->assertSee('Request Rental');
        $response->assertSee('Start Date');
        $response->assertSee('End Date');
        $response->assertSee('Purpose of Rental');
    }

    public function test_cannot_rent_own_equipment(): void
    {
        // Create category
        $category = Category::create([
            'name' => 'Projectors',
            'slug' => 'projectors',
            'icon' => 'fa-video'
        ]);

        // Create club admin user
        $club = User::forceCreate([
            'name' => 'Media Club Admin',
            'email' => 'media@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Media Club',
            'student_id' => '2223334'
        ]);

        // Create equipment owned by club admin
        $equipment = Equipment::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'HD Epson Projector',
            'description' => '3000 lumens portable projector.',
            'price_per_day' => 35.00,
            'condition' => 'new',
            'availability_status' => 'available',
            'pickup_location' => 'Kulliyyah of ICT Office'
        ]);

        $response = $this->actingAs($club)
                         ->get("/equipment/{$equipment->id}");

        $response->assertStatus(200);
        $response->assertSee('This is your equipment listing');
        $response->assertSee('Edit Listing');
        $response->assertDontSee('Rent This Equipment');
    }

    public function test_super_admin_can_rent_equipment_and_it_is_approved_immediately(): void
    {
        $category = Category::create([
            'name' => 'Audio',
            'slug' => 'audio',
            'icon' => 'fa-volume-up'
        ]);

        $club = User::forceCreate([
            'name' => 'Audio Club Admin',
            'email' => 'audio-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Audio Club',
            'student_id' => '9090909'
        ]);

        $superAdmin = User::forceCreate([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $equipment = Equipment::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'Wireless Microphone Set',
            'description' => 'Four wireless microphones.',
            'price_per_day' => 45.00,
            'condition' => 'excellent',
            'availability_status' => 'available',
            'pickup_location' => 'Audio Room'
        ]);

        $response = $this->actingAs($superAdmin)->get("/equipment/{$equipment->id}");

        $response->assertStatus(200);
        $response->assertSee('Rent This Equipment');

        $this->actingAs($superAdmin)->post("/equipment/{$equipment->id}/rent", [
            'start_date' => now()->addDay()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Super admin rental test',
        ])->assertRedirect(route('rentals.my-rentals'));

        $this->assertDatabaseHas('rentals', [
            'equipment_id' => $equipment->id,
            'borrower_id' => $superAdmin->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $equipment->id,
            'availability_status' => 'rented',
        ]);
    }

    public function test_availability_calendar_shows_blocked_dates(): void
    {
        $category = Category::create([
            'name' => 'Cameras',
            'slug' => 'cameras',
            'icon' => 'fa-camera'
        ]);

        $club = User::forceCreate([
            'name' => 'Photo Club Admin',
            'email' => 'photo@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Photo Club',
            'student_id' => '3334445'
        ]);

        $equipment = Equipment::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'Canon EOS R5',
            'description' => 'Professional mirrorless camera.',
            'price_per_day' => 80.00,
            'condition' => 'excellent',
            'availability_status' => 'available',
            'pickup_location' => 'Kulliyyah of ICT'
        ]);

        $borrower = User::forceCreate([
            'name' => 'Event Organizer',
            'email' => 'organizer@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'student_id' => '4445556'
        ]);

        \App\Models\Rental::create([
            'equipment_id' => $equipment->id,
            'borrower_id' => $borrower->id,
            'start_date' => now()->addDays(3)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'purpose' => 'Club event photography',
            'total_price' => 240.00,
            'status' => 'approved',
        ]);

        $response = $this->get("/equipment/{$equipment->id}");

        $response->assertStatus(200);
        $response->assertSee('Availability Calendar');
        $response->assertSee('Upcoming Bookings');
        $response->assertSee('Approved');
    }

    public function test_rental_rejected_when_dates_conflict(): void
    {
        $category = Category::create([
            'name' => 'Speakers',
            'slug' => 'speakers',
            'icon' => 'fa-volume-up'
        ]);

        $club = User::forceCreate([
            'name' => 'Audio Club Admin',
            'email' => 'audio@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Audio Club',
            'student_id' => '5556667'
        ]);

        $equipment = Equipment::create([
            'club_id' => $club->id,
            'category_id' => $category->id,
            'name' => 'PA Speaker System',
            'description' => 'Portable PA system for events.',
            'price_per_day' => 40.00,
            'condition' => 'good',
            'availability_status' => 'available',
            'pickup_location' => 'Student Union Building'
        ]);

        $existingBorrower = User::forceCreate([
            'name' => 'First Borrower',
            'email' => 'first@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'student_id' => '6667778'
        ]);

        $newBorrower = User::forceCreate([
            'name' => 'Second Borrower',
            'email' => 'second@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'student_id' => '7778889'
        ]);

        \App\Models\Rental::create([
            'equipment_id' => $equipment->id,
            'borrower_id' => $existingBorrower->id,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'purpose' => 'Existing booking',
            'total_price' => 120.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($newBorrower)->post("/equipment/{$equipment->id}/rent", [
            'start_date' => now()->addDays(3)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'purpose' => 'Overlapping event',
        ]);

        $response->assertRedirect(route('equipment.show', $equipment));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('rentals', 1);
    }
}
