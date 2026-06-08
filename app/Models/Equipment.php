<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id', 'category_id', 'name', 'description', 'image',
        'price_per_day', 'condition', 'availability_status', 'pickup_location'
    ];

    public function club()
    {
        return $this->belongsTo(User::class, 'club_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function isAvailable()
    {
        return $this->availability_status === 'available';
    }

    public function getFormattedPriceAttribute()
    {
        return 'RM ' . number_format($this->price_per_day, 2);
    }

    public function blockedRentals()
    {
        return $this->rentals()
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('start_date');
    }

    public function getBlockedDates(): array
    {
        $dates = [];

        $this->blockedRentals()
            ->get(['start_date', 'end_date'])
            ->each(function ($rental) use (&$dates) {
                $current = $rental->start_date->copy();
                while ($current <= $rental->end_date) {
                    $dates[] = $current->format('Y-m-d');
                    $current->addDay();
                }
            });

        return array_values(array_unique($dates));
    }

    public function hasDateConflict(string $startDate, string $endDate): bool
    {
        return $this->blockedRentals()
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->exists();
    }
}