<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConferenceRoomPivot extends Model
{
    protected $fillable = [
        'user_id',
        'conference_room_id',
        'is_default',
    ];

    /**
     * Get the user associated with the conference room pivot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the conference room associated with the pivot.
     */
    public function conferenceRoom()
    {
        return $this->belongsTo(ConferenceRoom::class);
    }
}
