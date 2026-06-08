<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndViewsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $memberUser;
    private Category $category;
    private Equipment $equipment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a category
        $this->category = Category::create([
            'name' => 'Tents',
            'slug' => 'tents',
            'icon' => 'fa-campground'
        ]);

        // Create club admin user
        $this->adminUser = User::forceCreate([
            'name' => 'Club Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Sports Club',
            'student_id' => '1112223'
        ]);

        // Create normal member user
        $this->memberUser = User::forceCreate([
            'name' => 'Member Student',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'student_id' => '7654321'
        ]);

        // Create equipment owned by admin
        $this->equipment = Equipment::create([
            'club_id' => $this->adminUser->id,
            'category_id' => $this->category->id,
            'name' => 'Double Dome Tent',
            'description' => 'A large double dome tent.',
            'price_per_day' => 25.00,
            'condition' => 'good',
            'availability_status' => 'available',
            'pickup_location' => 'IIUM Sports Center'
        ]);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Club Admin Dashboard');
        $response->assertSee('IIUM Sports Club');
        $response->assertSee('Total Equipment');
        $response->assertSee('Available');
        $response->assertSee('Rented');
        $response->assertSee('Maintenance');
        $response->assertSee('Recent Active Rentals');
        $response->assertDontSee('My Equipment Listings');
        $response->assertDontSee('Double Dome Tent');
    }

    public function test_super_admin_can_manage_clubs(): void
    {
        $superAdmin = User::forceCreate([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $borrower = User::forceCreate([
            'name' => 'Rental Borrower',
            'email' => 'borrower@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'student_id' => '9998887',
        ]);

        \App\Models\Rental::create([
            'equipment_id' => $this->equipment->id,
            'borrower_id' => $borrower->id,
            'start_date' => now()->addDay()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'purpose' => 'Testing club management counts',
            'total_price' => 50.00,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/clubs');

        $response->assertStatus(200);
        $response->assertSee('Manage Clubs');
        $response->assertSee('IIUM Sports Club');
        $response->assertSee('admin@example.com');
        $response->assertSee('Approved');
        $response->assertSee('1');
    }

    public function test_super_admin_can_reject_suspend_and_activate_clubs(): void
    {
        $superAdmin = User::forceCreate([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $pendingClub = User::forceCreate([
            'name' => 'Pending Club',
            'email' => 'pending-club@example.com',
            'password' => bcrypt('password'),
            'role' => 'pending_club',
            'club_name' => 'Pending Club',
            'club_status' => 'pending',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('admin.clubs.reject', $pendingClub), [
                'rejection_reason' => 'Missing documents',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $pendingClub->id,
            'club_status' => 'rejected',
            'rejection_reason' => 'Missing documents',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('admin.clubs.approve', $pendingClub))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $pendingClub->id,
            'role' => 'club_admin',
            'club_status' => 'approved',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('admin.clubs.suspend', $pendingClub), [
                'rejection_reason' => 'Policy review',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $pendingClub->id,
            'club_status' => 'suspended',
            'rejection_reason' => 'Policy review',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('admin.clubs.activate', $pendingClub))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $pendingClub->id,
            'role' => 'club_admin',
            'club_status' => 'approved',
            'rejection_reason' => null,
        ]);
    }

    public function test_member_can_access_member_dashboard(): void
    {
        $response = $this->actingAs($this->memberUser)
                         ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome back, Member Student');
        $response->assertSee('Active Rentals');
        $response->assertSee('Approved Rentals');
        $response->assertSee('Recent Booking History');
    }

    public function test_member_can_access_my_rentals(): void
    {
        $response = $this->actingAs($this->memberUser)
                         ->get('/my-rentals');

        $response->assertStatus(200);
        $response->assertSee('My Rental Requests');
        $response->assertSee('You have not submitted any rental requests yet.');
    }

    public function test_profile_page_loads_with_correct_data(): void
    {
        $response = $this->actingAs($this->memberUser)
                         ->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Account Settings');
        $response->assertSee('Member Student');
        $response->assertSee('student@example.com');
        $response->assertSee('7654321');
    }

    public function test_admin_can_access_equipment_create_form(): void
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/equipment/create');

        $response->assertStatus(200);
        $response->assertSee('Add New Equipment');
        $response->assertSee('Equipment Name');
        $response->assertSee('Price Per Day');
    }

    public function test_super_admin_can_select_club_when_creating_equipment(): void
    {
        $superAdmin = User::forceCreate([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($superAdmin)
                         ->get('/equipment/create');

        $response->assertStatus(200);
        $response->assertSee('Owning Club');
        $response->assertSee('IIUM Sports Club');

        $this->actingAs($superAdmin)->post('/equipment', [
            'club_id' => $this->adminUser->id,
            'category_id' => $this->category->id,
            'name' => 'Super Admin Camera',
            'description' => 'Created on behalf of a club.',
            'price_per_day' => 30,
            'condition' => 'good',
            'pickup_location' => 'Main Office',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('equipment', [
            'club_id' => $this->adminUser->id,
            'name' => 'Super Admin Camera',
        ]);
    }

    public function test_admin_can_access_equipment_edit_form(): void
    {
        $response = $this->actingAs($this->adminUser)
                         ->get("/equipment/{$this->equipment->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Equipment Listing');
        $response->assertSee('Double Dome Tent');
        $response->assertSee('Update Equipment');
    }
}
