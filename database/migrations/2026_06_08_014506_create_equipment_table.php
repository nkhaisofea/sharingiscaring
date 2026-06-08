<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->decimal('price_per_day', 10, 2);
            $table->enum('condition', ['new', 'excellent', 'good', 'fair', 'poor'])->default('good');
            $table->enum('availability_status', ['available', 'rented', 'maintenance'])->default('available');
            $table->string('pickup_location');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};