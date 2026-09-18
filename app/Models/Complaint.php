<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';
    protected $primaryKey = 'complaint_id';
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'submitted_by',
        'complainant_username',
        'respondent_username',
        'description',
        'complaint_type',
        'status',
        'resolution_notes',
        'created_at',
        'resolved_at',
    ];
}
