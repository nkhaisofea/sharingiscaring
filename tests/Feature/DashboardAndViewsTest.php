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
        $response->assertSee('Double Dome Tent');
    }

    public function test_member_can_access_member_dashboard(): void
    {
        $response = $this->actingAs($this->memberUser)
                         ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome back, Member Student');
        $response->assertSee('Active Rentals');
        $response->assertSee('Pending Requests');
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

    public function test_admin_can_access_pending_requests(): void
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/pending-requests');

        $response->assertStatus(200);
        $response->assertSee('Pending Rental Requests');
        $response->assertSee('All caught up! No pending requests.');
    }

    public function test_member_cannot_access_pending_requests_redirects(): void
    {
        $response = $this->actingAs($this->memberUser)
                         ->get('/pending-requests');

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
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
