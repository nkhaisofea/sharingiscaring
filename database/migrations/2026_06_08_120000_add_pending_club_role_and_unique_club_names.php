<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('member', 'club_admin', 'super_admin', 'pending_club') NOT NULL DEFAULT 'member'");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('club_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['club_name']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('member', 'club_admin', 'super_admin') NOT NULL DEFAULT 'member'");
        }
    }
};
