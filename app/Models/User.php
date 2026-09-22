<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password_hash',
        'role',
        'age',
        'gender',
        'address',
        'barangay',
        'contact_number',
        'skills',
        'certificate_proof',
        'is_verified',
        'rating',
        'profile_image_uri',
        'location_tag',
        'status',
        'privacy_consent_accepted',
        'privacy_consent_accepted_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'rating' => 'float',
        'age' => 'integer',
        'privacy_consent_accepted' => 'boolean',
        'privacy_consent_accepted_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    public function getRoleDisplayAttribute()
    {
        return match (strtolower($this->role)) {
            'skilled worker' => 'Skilled Worker',
            'residential' => 'Residential Client',
            'peso staff' => 'PESO Staff',
            'admin' => 'Administrator',
            default => ucfirst($this->role),
        };
    }
}