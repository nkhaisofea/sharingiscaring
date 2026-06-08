<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign In');
        $response->assertSee('Student Member');
        $response->assertSee('Club Admin');
        $response->assertSee('Student ID');
        $response->assertSee('Email');
        $response->assertSee('Password');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Register');
        $response->assertSee('Student ID');
        $response->assertSee('Club Name');
    }

    public function test_users_can_register_with_valid_iium_student_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ahmad Student',
            'student_id' => '2319875',
            'email' => 'ahmad@student.iium.edu.my',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        
        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Student',
            'student_id' => '2319875',
            'email' => 'ahmad@student.iium.edu.my',
            'role' => 'member', // Default role for registering
        ]);
    }

    public function test_club_admins_can_register_with_any_valid_email_and_club_name(): void
    {
        $response = $this->post('/register', [
            'role' => 'club_admin',
            'club_name' => 'IIUM Sports Club',
            'email' => 'musa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        
        $this->assertDatabaseHas('users', [
            'name' => 'IIUM Sports Club',
            'student_id' => null,
            'email' => 'musa@example.com',
            'role' => 'pending_club',
            'club_name' => 'IIUM Sports Club',
            'club_status' => 'pending',
        ]);
    }

    public function test_club_admin_registration_requires_unique_club_name(): void
    {
        User::forceCreate([
            'name' => 'IIUM Sports Club',
            'email' => 'sports@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pending_club',
            'club_name' => 'IIUM Sports Club',
        ]);

        $response = $this->post('/register', [
            'role' => 'club_admin',
            'club_name' => 'IIUM Sports Club',
            'email' => 'musa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'club_name' => 'This club name is already registered',
        ]);
        $this->assertGuest();
    }

    public function test_super_admin_cannot_register_publicly(): void
    {
        $response = $this->post('/register', [
            'name' => 'Super Admin',
            'role' => 'super_admin',
            'email' => 'super@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertGuest();
    }

    public function test_users_cannot_register_with_non_iium_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'External Person',
            'student_id' => '999999',
            'email' => 'external@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::forceCreate([
            'name' => 'Fatimah Student',
            'email' => 'fatimah@student.iium.edu.my',
            'password' => Hash::make('password123'),
            'student_id' => '2123456',
            'role' => 'member',
        ]);

        $response = $this->post('/login', [
            'role' => 'member',
            'student_id' => '2123456',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::forceCreate([
            'name' => 'Fatimah Student',
            'email' => 'fatimah@student.iium.edu.my',
            'password' => Hash::make('password123'),
            'student_id' => '2123456',
            'role' => 'member',
        ]);

        $response = $this->post('/login', [
            'role' => 'member',
            'student_id' => '2123456',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['student_id']);
        $this->assertGuest();
    }

    public function test_approved_club_admins_can_authenticate_with_email(): void
    {
        $user = User::forceCreate([
            'name' => 'IIUM Robotics Club',
            'email' => 'robotics@example.com',
            'password' => Hash::make('password123'),
            'role' => 'club_admin',
            'club_name' => 'IIUM Robotics Club',
        ]);

        $response = $this->post('/login', [
            'role' => 'club_admin',
            'email' => 'robotics@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_pending_club_admins_cannot_authenticate(): void
    {
        User::forceCreate([
            'name' => 'IIUM Pending Club',
            'email' => 'pending@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pending_club',
            'club_name' => 'IIUM Pending Club',
        ]);

        $response = $this->post('/login', [
            'role' => 'club_admin',
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::forceCreate([
            'name' => 'Fatimah Student',
            'email' => 'fatimah@student.iium.edu.my',
            'password' => Hash::make('password123'),
            'student_id' => '2123456',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)
                         ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
