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
        'estimated_budget',
        'preferred_schedule',
        'date_posted',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'user_id');
    }

    public function getEstimatedBudgetDisplayAttribute(): string
    {
        if (!empty($this->estimated_budget)) {
            $val = trim($this->estimated_budget);
            if (!str_starts_with($val, '₱')) {
                $cleaned = preg_replace('/[^0-9.]/', '', $val);
                if (is_numeric($cleaned) && (float)$cleaned > 0) {
                    return '₱' . number_format((float)$cleaned, 2);
                }
                return '₱' . $val;
            }
            return $val;
        }

        // Fallback: check if description contains "(Budget: ...)" or "Service Rate: ..."
        if (!empty($this->description)) {
            if (preg_match('/(?:Budget|Service Rate|Rate|Presyo):\s*([₱\d,.]+)/i', $this->description, $m)) {
                $raw = trim($m[1]);
                return str_starts_with($raw, '₱') ? $raw : ('₱' . $raw);
            }
        }

        return 'Negotiable';
    }
}
