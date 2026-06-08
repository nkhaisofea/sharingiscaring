<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id', 'borrower_id', 'start_date', 'end_date',
        'purpose', 'status', 'total_price', 'admin_notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-500',
            'approved' => 'bg-green-500',
            'rejected' => 'bg-red-500',
            'completed' => 'bg-blue-500',
            'cancelled' => 'bg-gray-500'
        ];
        
        return $badges[$this->status] ?? 'bg-gray-500';
    }

    public function getDurationInDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}