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
        'other_category',
        'evidence_files',
        'status',
        'resolution_notes',
        'resolution_decision',
        'sanction_status',
        'created_at',
        'resolved_at',
    ];

    public function getEvidenceListAttribute()
    {
        if (empty($this->evidence_files)) {
            return [];
        }
        $decoded = json_decode($this->evidence_files, true);
        return is_array($decoded) ? $decoded : [];
    }
}
