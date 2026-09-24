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
        'service_rate',
        'certificate_proof',
        'certificate_file',
        'valid_id_proof',
        'is_verified',
        'rating',
        'profile_image_uri',
        'location_tag',
        'status',
        'last_seen_at',
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
        'last_seen_at' => 'datetime',
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

    public function getServiceRateDisplayAttribute(): string
    {
        if (!empty($this->service_rate)) {
            $cleaned = trim($this->service_rate);
            if (str_starts_with($cleaned, '₱')) {
                return $cleaned;
            }
            if (is_numeric(preg_replace('/[^0-9.]/', '', $cleaned))) {
                return '₱' . number_format((float) preg_replace('/[^0-9.]/', '', $cleaned), 2);
            }
            return '₱' . $cleaned;
        }

        // Sensible defaults based on primary skill
        $skills = strtolower($this->skills ?? '');
        if (str_contains($skills, 'electric')) return '₱650.00';
        if (str_contains($skills, 'plumb')) return '₱600.00';
        if (str_contains($skills, 'carpent')) return '₱550.00';
        if (str_contains($skills, 'appliance')) return '₱600.00';
        if (str_contains($skills, 'paint') || str_contains($skills, 'mason')) return '₱500.00';
        return '₱500.00';
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

    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        return $this->last_seen_at->gt(now()->subMinutes(5));
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_online ? 'Active' : 'Offline';
    }

    public function getLastSeenDisplayAttribute(): string
    {
        if ($this->is_online) {
            return 'Active now';
        }
        if (!$this->last_seen_at) {
            return 'Never';
        }
        return $this->last_seen_at->diffForHumans();
    }

    public function getCreatedAtDisplayAttribute(): string
    {
        if ($this->created_at) {
            return $this->created_at->format('M d, Y h:i A');
        }
        if ($this->date_created) {
            try {
                return \Carbon\Carbon::parse($this->date_created)->format('M d, Y h:i A');
            } catch (\Exception $e) {
                return (string) $this->date_created;
            }
        }
        return 'N/A';
    }
}