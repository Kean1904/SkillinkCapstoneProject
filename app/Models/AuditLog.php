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

    const MIN_LOGS = 20;
    const MAX_LOGS = 30;

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
     * Enforce bounds:
     * - Minimum: 20 logs
     * - Maximum: 30 logs
     * - When total logged events exceeds 30, it automatically resets to the 20 most recent logs.
     * - If total logs is below 20, seed baseline audit entries to maintain minimum 20 logs.
     */
    public static function enforceBounds()
    {
        $count = self::count();

        // 1. If exceeds maximum (30 logs), reset to the latest 20 logs
        if ($count > self::MAX_LOGS) {
            $keepIds = self::latest('log_id')->take(self::MIN_LOGS)->pluck('log_id')->toArray();
            if (!empty($keepIds)) {
                self::whereNotIn('log_id', $keepIds)->delete();
            }
            $count = self::count();
        }

        // 2. If below minimum (20 logs), seed baseline records
        if ($count < self::MIN_LOGS) {
            $needed = self::MIN_LOGS - $count;
            self::seedBaselineLogs($needed);
            $count = self::count();
        }

        return $count;
    }

    /**
     * Manually reset audit logs to baseline minimum (20 logs).
     */
    public static function resetLogs()
    {
        $keepIds = self::latest('log_id')->take(self::MIN_LOGS)->pluck('log_id')->toArray();
        if (!empty($keepIds)) {
            self::whereNotIn('log_id', $keepIds)->delete();
        }

        $count = self::count();
        if ($count < self::MIN_LOGS) {
            self::seedBaselineLogs(self::MIN_LOGS - $count);
        }

        // Add a reset log event
        self::create([
            'user_id' => Session::get('user_id'),
            'actor_name' => Session::get('user_name', 'Administrator'),
            'actor_role' => 'Administrator',
            'action' => 'AUDIT_LOGS_RESET',
            'details' => 'Audit log history was reset to the baseline minimum of 20 events.',
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'status' => 'Success',
        ]);

        $keepIds = self::latest('log_id')->take(self::MIN_LOGS)->pluck('log_id')->toArray();
        if (!empty($keepIds)) {
            self::whereNotIn('log_id', $keepIds)->delete();
        }

        return self::count();
    }

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

        $entry = self::create([
            'user_id' => $userId,
            'actor_name' => $resolvedActorName,
            'actor_role' => ucwords($resolvedActorRole),
            'action' => $action,
            'details' => $details,
            'ip_address' => $ip,
            'status' => $status,
        ]);

        // When exceeding maximum of 30 logs, automatically reset to the latest 20 logs
        if (self::count() > self::MAX_LOGS) {
            $keepIds = self::latest('log_id')->take(self::MIN_LOGS)->pluck('log_id')->toArray();
            if (!empty($keepIds)) {
                self::whereNotIn('log_id', $keepIds)->delete();
            }
        }

        return $entry;
    }

    /**
     * Seed baseline municipal system audit entries if count is below 20.
     */
    public static function seedBaselineLogs($count = 20)
    {
        $templates = [
            ['action' => 'SYSTEM_INITIALIZATION', 'role' => 'Administrator', 'actor' => 'System Engine', 'details' => 'SKILLINK Magalang core security engine started and synchronized.'],
            ['action' => 'SECURITY_FIREWALL_VERIFY', 'role' => 'Administrator', 'actor' => 'Security Module', 'details' => 'Data Privacy RA 10173 integrity protocol verified with zero tampering.'],
            ['action' => 'PESO_STATION_SYNC', 'role' => 'PESO Staff', 'actor' => 'PESO Magalang', 'details' => 'Municipal labor exchange synchronized with local PESO central terminal.'],
            ['action' => 'ACCREDITATION_VERIFICATION', 'role' => 'PESO Staff', 'actor' => 'PESO Screener', 'details' => 'Routine credentials inspection and TESDA certification check executed.'],
            ['action' => 'WORKER_PROFILE_INSPECTION', 'role' => 'PESO Staff', 'actor' => 'Officer Staff', 'details' => 'Labor classification and barangay mapping validated for Magalang trades.'],
            ['action' => 'RESIDENTIAL_PORTAL_ACTIVE', 'role' => 'Residential', 'actor' => 'Municipal Resident', 'details' => 'Household service booking portal checked and active for neighborhood jobs.'],
            ['action' => 'JOB_MATCHING_OPTIMIZATION', 'role' => 'Administrator', 'actor' => 'Algorithm Engine', 'details' => 'Proximity scoring and skill weighting recalibrated for Poblacion cluster.'],
            ['action' => 'DATABASE_BACKUP_SNAPSHOT', 'role' => 'Administrator', 'actor' => 'DB Maintenance', 'details' => 'Automated snapshot backup generated for user accounts and service logs.'],
            ['action' => 'MUNICIPAL_ADVISORY_CHECK', 'role' => 'Administrator', 'actor' => 'Admin Broadcast', 'details' => 'Municipal emergency notice protocol and workforce advisory verified.'],
            ['action' => 'WORKFORCE_ANALYTICS_UPDATE', 'role' => 'PESO Staff', 'actor' => 'DOLE Analytics', 'details' => 'Employment metrics updated for Magalang local workforce report.'],
        ];

        for ($i = 0; $i < $count; $i++) {
            $tpl = $templates[$i % count($templates)];
            self::create([
                'user_id' => null,
                'actor_name' => $tpl['actor'],
                'actor_role' => $tpl['role'],
                'action' => $tpl['action'],
                'details' => $tpl['details'],
                'ip_address' => '127.0.0.1',
                'status' => 'Success',
            ]);
        }
    }
}
