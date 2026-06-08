<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Seed users for each application role.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'IIUM Sports Club Admin',
                'email' => 'sports.club@iium.edu.my',
                'student_id' => 'STAFF001',
                'role' => 'club_admin',
                'club_name' => 'IIUM Sports Club',
            ],
            [
                'name' => 'Super Admin',
                'email' => 'super.admin@iium.edu.my',
                'student_id' => 'STAFF002',
                'role' => 'super_admin',
                'club_name' => null,
            ],
            [
                'name' => 'Member One',
                'email' => 'member.one@student.iium.edu.my',
                'student_id' => 'MEMBER001',
                'role' => 'member',
                'club_name' => null,
            ],
            [
                'name' => 'Member Two',
                'email' => 'member.two@student.iium.edu.my',
                'student_id' => 'MEMBER002',
                'role' => 'member',
                'club_name' => null,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
