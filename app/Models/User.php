<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'club_name', 'student_id',
        'club_status', 'rejection_reason', 'suspended_at',
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Get the equipment owned by this club admin.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'club_id');
    }

    /**
     * Get all rentals made by this user as a borrower.
     */
    public function rentalsAsBorrower()
    {
        return $this->hasMany(Rental::class, 'borrower_id');
    }

    /**
     * Get rentals for equipment owned by this club.
     */
    public function clubRentals()
    {
        return $this->hasManyThrough(Rental::class, Equipment::class, 'club_id', 'equipment_id');
    }

    /**
     * Check if user is a club admin.
     */
    public function isClubAdmin()
    {
        return $this->role === 'club_admin'
            && in_array($this->club_status, [null, 'approved'], true);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is waiting for club admin approval.
     */
    public function isPendingClub()
    {
        return $this->role === 'pending_club'
            && in_array($this->club_status, [null, 'pending'], true);
    }

    public function isRejectedClub()
    {
        return $this->club_status === 'rejected';
    }

    public function isSuspendedClub()
    {
        return $this->club_status === 'suspended';
    }

    /**
     * Get user's initials for avatar.
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }
}
