<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceRoom extends Model
{
    protected $fillable = [
        'name',
        'location',
        'capacity',
        'is_available',
        'description',
    ];

    /**
     * Get the name of the conference room.
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Scope a query to only include available conference rooms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Get the meetings associated with the conference room.
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Get the users associated with the conference room.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_conference_room_pivots')
                    ->withPivot('is_default')
                    ->withTimestamps();
    }
}
