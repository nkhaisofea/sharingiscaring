<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('club_status', ['pending', 'approved', 'rejected', 'suspended'])->nullable()->after('club_name');
            $table->text('rejection_reason')->nullable()->after('club_status');
            $table->timestamp('suspended_at')->nullable()->after('rejection_reason');
        });

        DB::table('users')->where('role', 'club_admin')->update(['club_status' => 'approved']);
        DB::table('users')->where('role', 'pending_club')->update(['club_status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['club_status', 'rejection_reason', 'suspended_at']);
        });
    }
};
