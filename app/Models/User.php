<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser; // Import the FilamentUser contract
use Filament\Panel; // Import the Panel class
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser // Implement the FilamentUser contract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    /**
     * Get the conference rooms associated with the user.
     */

    public function conferenceRooms()
    {
        return $this->belongsToMany(ConferenceRoom::class, 'user_conference_room_pivots')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Determine if the user can access the Filament panel.
     *
     * @param  \Filament\Panel  $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Example: Allow only users with the 'admin' role to access the panel.
        return $this->hasRole('super_admin');

        // You can customize this logic based on your authorization needs.
        // For instance, you could check for specific email domains, teams, or other attributes.
        // If you don't have any specific rules and want to allow all authenticated users,
        // you can simply return true; (less secure in production without other authorization mechanisms).
        // return true;
    }
}
