<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'actor_name',
        'actor_role',
        'action',
        'details',
        'ip_address',
        'status',
    ];

    /**
     * Record an audit event into the database.
     *
     * @param string $action
     * @param string|null $details
     * @param string|null $actorName
     * @param string|null $actorRole
     * @param int|null $userId
     * @param string $status
     * @return AuditLog
     */
    public static function log($action, $details = null, $actorName = null, $actorRole = null, $userId = null, $status = 'Success')
    {
        $resolvedActorName = $actorName ?? Session::get('user_name') ?? 'Guest';
        $resolvedActorRole = $actorRole ?? Session::get('user_role') ?? 'User';
        $ip = request()->ip() ?? '127.0.0.1';

        return self::create([
            'user_id' => $userId,
            'actor_name' => $resolvedActorName,
            'actor_role' => ucwords($resolvedActorRole),
            'action' => $action,
            'details' => $details,
            'ip_address' => $ip,
            'status' => $status,
        ]);
    }
}
