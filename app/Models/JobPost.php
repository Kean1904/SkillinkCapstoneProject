<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    protected $table = 'service_requests';
    protected $primaryKey = 'request_id';

    protected $fillable = [
        'title',
        'client_id',
        'posted_by',
        'peso_staff_id',
        'applicant_username',
        'category',
        'description',
        'location_tag',
        'barangay',
        'preferred_schedule',
        'date_posted',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'user_id');
    }
}
