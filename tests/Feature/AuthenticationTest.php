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
        $response->assertSee('Email');
        $response->assertSee('Password');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Register');
        $response->assertSee('Student ID');
        $response->assertSee('IIUM Email');
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

    public function test_users_can_register_with_valid_iium_staff_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr Musa',
            'student_id' => '99123',
            'email' => 'musa@iium.edu.my',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        
        $this->assertDatabaseHas('users', [
            'name' => 'Dr Musa',
            'student_id' => '99123',
            'email' => 'musa@iium.edu.my',
        ]);
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
            'email' => 'fatimah@student.iium.edu.my',
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
            'email' => 'fatimah@student.iium.edu.my',
            'password' => 'wrongpassword',
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
