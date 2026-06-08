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
}