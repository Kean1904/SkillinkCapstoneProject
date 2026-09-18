<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'booking_reference',
        'request_id',
        'worker_id',
        'client_username',
        'worker_username',
        'client_name',
        'worker_name',
        'service_category',
        'task_description',
        'service_address',
        'barangay',
        'estimated_budget',
        'scheduled_date',
        'status',
        'completion_date',
    ];
}
