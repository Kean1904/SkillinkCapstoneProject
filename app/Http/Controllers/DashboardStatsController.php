<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobPost;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardStatsController extends Controller
{
    /**
     * Unified Real-time Live Stats for Web Dashboards & Mobile Application
     */
    public function getLiveStats(Request $request)
    {
        $currentUsername = Session::get('user_name') ?? $request->query('username');
        if (!empty($currentUsername)) {
            User::where('name', $currentUsername)->update(['last_seen_at' => now()]);
        }

        // 1. Available Jobs: Open jobs ready for application (no applicant assigned yet)
        $availableJobs = JobPost::where(function ($q) {
            $q->whereNull('applicant_username')
              ->orWhere('applicant_username', '');
        })->whereNotIn('status', ['Completed', 'Cancelled'])->count();

        // 2. Pending Jobs: Jobs that have been applied for and are pending review/action
        $pendingJobs = JobPost::whereNotNull('applicant_username')
            ->where('applicant_username', '!=', '')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();

        // 3. Total active jobs & completed jobs
        $totalJobs = JobPost::count();
        $completedJobs = Booking::where('status', 'COMPLETED')->count()
            + JobPost::where('status', 'Completed')->count();

        // 4. Workers & Residents
        $availableWorkers = User::where('role', 'skilled worker')->where('is_verified', true)->count();
        $totalWorkers = User::where('role', 'skilled worker')->count();
        $residential = User::where('role', 'residential')->count();
        $staffCount = User::where('role', 'peso staff')->count();
        $adminCount = User::where('role', 'admin')->count();

        // 5. Complaints & Audit
        $complaints = Complaint::where('status', '!=', 'Resolved')->count();
        $auditLogsCount = \App\Models\AuditLog::enforceBounds();

        // 6. User-specific counts
        $myAppliedJobs = 0;
        $myPostedJobs = 0;
        if (!empty($currentUsername)) {
            $myAppliedJobs = JobPost::where('applicant_username', $currentUsername)
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count();

            $myPostedJobs = JobPost::where('posted_by', $currentUsername)
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count();
        }

        // 7. Latest Active Jobs list for real-time feed updates
        $jobs = JobPost::whereNotIn('status', ['Completed', 'Cancelled'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($job) use ($currentUsername) {
                $isAppliedByMe = !empty($currentUsername) && ($job->applicant_username === $currentUsername);
                $isTaken = !empty($job->applicant_username);

                return [
                    'request_id'         => (string) ($job->request_id ?? $job->getKey()),
                    'title'              => $job->title,
                    'category'           => $job->category,
                    'description'        => $job->description,
                    'barangay'           => $job->barangay,
                    'posted_by'          => $job->posted_by,
                    'date_posted'        => $job->date_posted,
                    'status'             => $job->status,
                    'applicant_username' => $job->applicant_username,
                    'is_applied_by_me'   => $isAppliedByMe,
                    'is_taken'           => $isTaken,
                    'can_apply'          => !$isTaken,
                    'apply_url'          => route('skilled_worker.job.apply', $job->request_id ?? $job->getKey()),
                ];
            });

        return response()->json([
            'success'          => true,
            'timestamp'        => now()->toIso8601String(),
            'availableJobs'    => $availableJobs,
            'pendingJobs'      => $pendingJobs,
            'totalJobs'        => $totalJobs,
            'completedJobs'    => $completedJobs,
            'availableWorkers' => $availableWorkers,
            'totalWorkers'     => $totalWorkers,
            'residential'      => $residential,
            'staffCount'       => $staffCount,
            'adminCount'       => $adminCount,
            'complaints'       => $complaints,
            'auditLogsCount'   => $auditLogsCount,
            'myAppliedJobs'    => $myAppliedJobs,
            'myPostedJobs'     => $myPostedJobs,
            'jobsList'         => $jobs,
        ], 200);
    }
}
