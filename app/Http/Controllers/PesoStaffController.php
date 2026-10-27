<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobPost;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PesoStaffController extends Controller
{
    private function getCurrentStaff()
    {
        $username = Session::get('user_name');
        return User::where('name', $username)->first()
            ?? User::where('role', 'peso staff')->first()
            ?? new User(['name' => 'peso_officer@Staff', 'first_name' => 'Grace', 'last_name' => 'Manalo']);
    }

    public function index(Request $request)
    {
        $availableJobs = JobPost::where(function ($q) {
            $q->whereNull('applicant_username')
              ->orWhere('applicant_username', '');
        })->whereNotIn('status', ['Completed', 'Cancelled'])->count();

        $pendingJobs = JobPost::whereNotNull('applicant_username')
            ->where('applicant_username', '!=', '')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();

        $skilledWorkers = User::where('role', 'skilled worker')->count();
        $residential = User::where(function($q) {
            $q->where('role', 'residential')->orWhere('role', 'like', '%household%')->orWhere('role', 'like', '%client%');
        })->count();
        $complaintsCount = Complaint::where('status', '!=', 'Resolved')->count();

        $accreditationQueue = User::where('role', 'skilled worker')
            ->where('is_verified', false)
            ->latest()
            ->get();

        $verifiedWorkers = User::where('role', 'skilled worker')
            ->where('is_verified', true)
            ->latest()
            ->get();

        $jobsList = JobPost::latest()->get();
        $bookingsList = Booking::latest()->get();
        $complaintsList = Complaint::latest()->get();
        $workerActivities = \App\Models\AuditLog::where(function($q) {
            $q->where('actor_role', 'like', '%worker%')
              ->orWhere('action', 'like', '%BOOKING%')
              ->orWhere('action', 'like', '%JOB%')
              ->orWhere('action', 'like', '%CREDENTIAL%');
        })->latest()->take(30)->get();

        return view('dashboard.PesoStaff', compact(
            'availableJobs',
            'pendingJobs',
            'skilledWorkers',
            'residential',
            'complaintsCount',
            'accreditationQueue',
            'verifiedWorkers',
            'jobsList',
            'bookingsList',
            'complaintsList',
            'workerActivities'
        ));
    }

    public function accreditation()
    {
        $workers = User::where('role', 'skilled worker')->latest()->get();
        return view('peso_staff.accreditation', compact('workers'));
    }

    public function jobTracking()
    {
        $jobs = JobPost::latest()->get();
        $bookings = Booking::latest()->get();
        $workerActivities = \App\Models\AuditLog::where(function($q) {
            $q->where('actor_role', 'like', '%worker%')
              ->orWhere('action', 'like', '%BOOKING%')
              ->orWhere('action', 'like', '%JOB%')
              ->orWhere('action', 'like', '%CREDENTIAL%')
              ->orWhere('action', 'like', '%SKILL%');
        })->latest()->take(60)->get();

        return view('peso_staff.job_tracking', compact('jobs', 'bookings', 'workerActivities'));
    }

    public function complaints()
    {
        $complaints = Complaint::latest('complaint_id')->get();
        return view('peso_staff.complaints', compact('complaints'));
    }

    public static function mapTradeToTesdaQualification($rawSkill)
    {
        $skill = strtolower(trim($rawSkill ?: 'General Handyman'));
        
        if (str_contains($skill, 'electr') || str_contains($skill, 'kuryente') || str_contains($skill, 'wir')) {
            return [
                'qualification' => 'Electrical Installation & Maintenance NC II',
                'code'          => 'EET-EIM-NC2',
                'sector'        => 'Electrical & Electronics',
                'delivery'      => 'Mobile Training Program (MTP) / On-Site Assessment',
            ];
        }
        if (str_contains($skill, 'plumb') || str_contains($skill, 'tubo') || str_contains($skill, 'poso')) {
            return [
                'qualification' => 'Plumbing NC II',
                'code'          => 'CON-PLB-NC2',
                'sector'        => 'Construction',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'carp') || str_contains($skill, 'karpint') || str_contains($skill, 'wood')) {
            return [
                'qualification' => 'Carpentry NC II',
                'code'          => 'CON-CRP-NC2',
                'sector'        => 'Construction',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'weld') || str_contains($skill, 'hinang') || str_contains($skill, 'smaw')) {
            return [
                'qualification' => 'Shielded Metal Arc Welding (SMAW) NC II',
                'code'          => 'MET-SMA-NC2',
                'sector'        => 'Metals & Engineering',
                'delivery'      => 'On-Site Assessment / Assessment Center',
            ];
        }
        if (str_contains($skill, 'aircon') || str_contains($skill, 'rac') || str_contains($skill, 'refrig') || str_contains($skill, 'hvac')) {
            return [
                'qualification' => 'RAC Servicing (DOMRAC) NC II',
                'code'          => 'EET-RAC-NC2',
                'sector'        => 'Electrical & Electronics',
                'delivery'      => 'Mobile Training Program (MTP)',
            ];
        }
        if (str_contains($skill, 'appliance') || str_contains($skill, 'electronics') || str_contains($skill, 'tv repair')) {
            return [
                'qualification' => 'Consumer Electronics Servicing NC II',
                'code'          => 'ELC-CES-NC2',
                'sector'        => 'Electrical & Electronics',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'mason') || str_contains($skill, 'semento') || str_contains($skill, 'palitada')) {
            return [
                'qualification' => 'Masonry NC II',
                'code'          => 'CON-MSN-NC2',
                'sector'        => 'Construction',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'paint') || str_contains($skill, 'pintor')) {
            return [
                'qualification' => 'Construction Painting NC II',
                'code'          => 'CON-CPT-NC2',
                'sector'        => 'Construction',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'tile') || str_contains($skill, 'baldosa')) {
            return [
                'qualification' => 'Tile Setting NC II',
                'code'          => 'CON-TLS-NC2',
                'sector'        => 'Construction',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'auto') || str_contains($skill, 'mechanic') || str_contains($skill, 'motor')) {
            return [
                'qualification' => 'Automotive Servicing NC II',
                'code'          => 'ALT-ASV-NC2',
                'sector'        => 'Automotive & Land Transportation',
                'delivery'      => 'Assessment Center / MTP',
            ];
        }
        if (str_contains($skill, 'computer') || str_contains($skill, 'it') || str_contains($skill, 'css') || str_contains($skill, 'network')) {
            return [
                'qualification' => 'Computer Systems Servicing NC II',
                'code'          => 'ICT-CSS-NC2',
                'sector'        => 'Information & Communications Tech',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'housekeep') || str_contains($skill, 'clean')) {
            return [
                'qualification' => 'Housekeeping NC II',
                'code'          => 'TRS-HSK-NC2',
                'sector'        => 'Tourism & Hospitality',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        if (str_contains($skill, 'cook') || str_contains($skill, 'chef') || str_contains($skill, 'kusin')) {
            return [
                'qualification' => 'Cookery NC II',
                'code'          => 'TRS-COK-NC2',
                'sector'        => 'Tourism & Hospitality',
                'delivery'      => 'Community-Based Training Program (CBTP)',
            ];
        }
        
        $cleanName = ucwords(trim($rawSkill ?: 'General Handyman'));
        $slug = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $cleanName), 0, 4));
        return [
            'qualification' => $cleanName . ' NC II',
            'code'          => 'TESDA-' . ($slug ?: 'GEN') . '-NC2',
            'sector'        => 'General Building & Community Services',
            'delivery'      => 'Community-Based Training Program (CBTP)',
        ];
    }

    public function reports()
    {
        $allWorkers = User::where('role', 'skilled worker')->get();
        $totalWorkers = $allWorkers->count();

        $certifiedWorkers = $allWorkers->filter(function($w) {
            return (bool)$w->is_verified && !empty($w->certificate_proof) && 
                (stripos($w->certificate_proof, 'TESDA') !== false || stripos($w->certificate_proof, 'NC') !== false);
        })->values();
        $certifiedCount = $certifiedWorkers->count();

        $uncertifiedWorkers = $allWorkers->filter(function($w) use ($certifiedWorkers) {
            return !$certifiedWorkers->contains('user_id', $w->user_id);
        })->values();
        $uncertifiedCount = $uncertifiedWorkers->count();

        $certificationRate = $totalWorkers > 0 ? round(($certifiedCount / $totalWorkers) * 100, 1) : 0;

        $tradesNeedingTraining = [];
        foreach ($uncertifiedWorkers as $w) {
            $tradeList = array_map('trim', explode(',', $w->skills ?: 'General Handyman'));
            foreach ($tradeList as $t) {
                if (!empty($t)) {
                    $tradesNeedingTraining[$t] = ($tradesNeedingTraining[$t] ?? 0) + 1;
                }
            }
        }
        arsort($tradesNeedingTraining);
        $prioritySkillsCount = count($tradesNeedingTraining);

        // Map trades to formal TESDA NC Qualifications with exact worker counts and course codes
        $qualificationsMap = [];
        $totalTradeInstances = 0;
        foreach ($tradesNeedingTraining as $rawTrade => $count) {
            $meta = self::mapTradeToTesdaQualification($rawTrade);
            $qTitle = $meta['qualification'];
            if (!isset($qualificationsMap[$qTitle])) {
                $qualificationsMap[$qTitle] = [
                    'qualification_title' => $qTitle,
                    'course_code'         => $meta['code'],
                    'sector'              => $meta['sector'],
                    'delivery_mode'       => $meta['delivery'],
                    'raw_trades'          => [$rawTrade],
                    'worker_count'        => $count,
                ];
            } else {
                $qualificationsMap[$qTitle]['worker_count'] += $count;
                if (!in_array($rawTrade, $qualificationsMap[$qTitle]['raw_trades'])) {
                    $qualificationsMap[$qTitle]['raw_trades'][] = $rawTrade;
                }
            }
            $totalTradeInstances += $count;
        }

        uasort($qualificationsMap, fn($a, $b) => $b['worker_count'] <=> $a['worker_count']);

        $qualificationsNeeded = [];
        $itemNum = 1;
        foreach ($qualificationsMap as $q) {
            $share = $totalTradeInstances > 0 ? round(($q['worker_count'] / $totalTradeInstances) * 100, 1) : 0;
            $priority = $q['worker_count'] >= 5 ? 'URGENT' : ($q['worker_count'] >= 2 ? 'HIGH' : 'STANDARD');
            $qualificationsNeeded[] = [
                'item_no'             => $itemNum++,
                'qualification_title' => $q['qualification_title'],
                'course_code'         => $q['course_code'],
                'sector'              => $q['sector'],
                'delivery_mode'       => $q['delivery_mode'],
                'worker_count'        => $q['worker_count'],
                'share_percentage'    => $share,
                'priority_level'      => $priority,
                'raw_trades'          => implode(', ', $q['raw_trades']),
            ];
        }

        $uncertifiedByBarangay = [];
        foreach ($uncertifiedWorkers as $w) {
            $bgy = !empty($w->barangay) ? trim($w->barangay) : 'Unassigned';
            if (!isset($uncertifiedByBarangay[$bgy])) {
                $uncertifiedByBarangay[$bgy] = [
                    'barangay' => $bgy,
                    'count' => 0,
                    'trades' => [],
                ];
            }
            $uncertifiedByBarangay[$bgy]['count']++;
            $tradeList = array_map('trim', explode(',', $w->skills ?: 'General Handyman'));
            foreach ($tradeList as $t) {
                if (!in_array($t, $uncertifiedByBarangay[$bgy]['trades'])) {
                    $uncertifiedByBarangay[$bgy]['trades'][] = $t;
                }
            }
        }
        uasort($uncertifiedByBarangay, fn($a, $b) => $b['count'] <=> $a['count']);
        $uncertifiedByBarangay = array_values($uncertifiedByBarangay);

        return view('peso_staff.reports', compact(
            'allWorkers',
            'totalWorkers',
            'certifiedWorkers',
            'certifiedCount',
            'uncertifiedWorkers',
            'uncertifiedCount',
            'certificationRate',
            'tradesNeedingTraining',
            'prioritySkillsCount',
            'uncertifiedByBarangay',
            'qualificationsNeeded'
        ));
    }

    public function announcements()
    {
        $jobs = JobPost::latest()->get();
        $announcements = \App\Models\Announcement::latest()->get();
        return view('peso_staff.announcements', compact('jobs', 'announcements'));
    }

    public function profile()
    {
        $user = $this->getCurrentStaff();
        return view('peso_staff.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $this->getCurrentStaff();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:50',
            'barangay' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:15|max:120',
            'gender' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->contact_number = $request->contact_number;
        $user->barangay = $request->barangay;
        $user->address = $request->address;
        if ($request->filled('age')) $user->age = $request->age;
        if ($request->filled('gender')) $user->gender = $request->gender;

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = 'avatar_' . $user->user_id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $destinationPath = public_path('uploads/avatars');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $avatar->move($destinationPath, $filename);
            $user->profile_image_uri = 'uploads/avatars/' . $filename;
            Session::put('profile_image_uri', $user->profile_image_uri);
        }

        $user->save();
        Session::put('full_name', $user->first_name . ' ' . $user->last_name);

        return back()->with('success', 'Your staff profile details have been updated successfully!');
    }

    public function settings()
    {
        $user = $this->getCurrentStaff();
        return view('peso_staff.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user = $this->getCurrentStaff();
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Staff password updated successfully!');
    }

    public function viewCredentials($id)
    {
        $worker = User::findOrFail($id);
        return view('peso_staff.worker_credentials', compact('worker'));
    }

    public function accreditWorker(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->verification_status = 'verified';
        $user->rejection_reason = null;
        $user->save();

        \App\Models\AuditLog::log(
            'ACCREDITATION_APPROVED',
            "PESO Staff officially verified and accredited Skilled Worker: {$user->full_name} ({$user->skills}).",
            Session::get('user_name', 'PESO Staff'),
            'PESO Staff',
            $user->user_id
        );

        return back()->with('success', "Worker {$user->full_name} has been officially accredited by PESO Magalang!");
    }

    public function unaccreditWorker(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $reason = $request->input('rejection_reason', 'Hindi pumasa sa pagsusuri ng PESO Staff.');
        $user->is_verified = false;
        $user->verification_status = 'rejected';
        $user->rejection_reason = $reason;
        $user->save();

        \App\Models\AuditLog::log(
            'ACCREDITATION_REVOKED',
            "PESO Staff denied/revoked accreditation status for worker: {$user->full_name}. Reason: {$reason}",
            Session::get('user_name', 'PESO Staff'),
            'PESO Staff',
            $user->user_id
        );

        return back()->with('success', "Worker {$user->full_name} status updated to Denied. Reason: {$reason}");
    }

    public function requestReupload(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $reason = $request->input('rejection_reason', 'Kailangan ng mas malinaw o kumpletong kopya ng dokumento / ID / TESDA Certification.');
        $user->is_verified = false;
        $user->verification_status = 're_upload';
        $user->rejection_reason = $reason;
        $user->save();

        \App\Models\AuditLog::log(
            'ACCREDITATION_REUPLOAD_REQUESTED',
            "PESO Staff requested document re-upload for worker: {$user->full_name}. Reason: {$reason}",
            Session::get('user_name', 'PESO Staff'),
            'PESO Staff',
            $user->user_id
        );

        return back()->with('success', "Naabisuhan na si {$user->full_name} na kailangan mag-Re-Upload ng dokumento. Dahilan: {$reason}");
    }

    /**
     * Broadcast Municipal Announcement / Maintenance Advisory
     */
    public function broadcastAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'message' => 'required|string',
            'target_audience' => 'required|string|in:all,skilled_worker,residential',
        ]);

        // Persist announcement in database for dashboards (Workers and Clients)
        $announcement = \App\Models\Announcement::create([
            'title' => trim($request->title),
            'category' => trim($request->category),
            'message' => trim($request->message),
            'target_audience' => trim($request->target_audience),
            'posted_by' => Session::get('user_name', 'PESO Staff Magalang'),
        ]);

        $query = User::whereNotNull('email')->where('email', '!=', '');
        if ($request->target_audience === 'skilled_worker') {
            $query->where('role', 'skilled worker');
        } elseif ($request->target_audience === 'residential' || $request->target_audience === 'household') {
            $query->where(function($q) {
                $q->where('role', 'residential')->orWhere('role', 'like', '%household%')->orWhere('role', 'like', '%client%');
            });
        }

        $recipients = $query->get();

        \App\Models\AuditLog::log(
            'ANNOUNCEMENT_BROADCAST',
            "Broadcasted municipal notice: '{$request->title}' ({$request->category}) for {$request->target_audience} to {$recipients->count()} recipients.",
            Session::get('user_name', 'PESO Staff'),
            'PESO Staff',
            Session::get('user_id')
        );

        $targetText = $request->target_audience === 'skilled_worker' ? 'Skilled Workers lamang' : ($request->target_audience === 'residential' ? 'Household Clients lamang' : 'lahat ng rehistradong gumagamit');

        return back()->with('success', "Municipal announcement ('{$request->title}') posted successfully para sa {$targetText} at naka-broadcast sa {$recipients->count()} accounts!");
    }

    public function postOutsideJob(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'city_province'     => 'required|string|max:150',
            'worksite_location' => 'required|string|max:255',
            'employer'          => 'required|string|max:200',
            'salary'            => 'required|string|max:150',
            'vacancies'         => 'required|integer|min:1|max:500',
            'schedule'          => 'required|string|max:100',
            'description'       => 'required|string',
        ]);

        $descriptionText = "【REGIONAL EMPLOYMENT - OUTSIDE MAGALANG】\n"
            . "• Employer / Partner: " . trim($request->employer) . "\n"
            . "• Worksite / City: " . trim($request->worksite_location) . ", " . trim($request->city_province) . "\n"
            . "• Compensation: " . trim($request->salary) . "\n"
            . "• Open Vacancies: " . intval($request->vacancies) . " Skilled Worker(s)\n"
            . "• Schedule: " . trim($request->schedule) . "\n\n"
            . "• Job Scope & Requirements:\n" . trim($request->description);

        $job = JobPost::create([
            'title'              => trim($request->title),
            'client_id'          => Session::get('user_id', 1),
            'posted_by'          => 'PESO Magalang (Regional Employment Desk)',
            'category'           => trim($request->category),
            'description'        => $descriptionText,
            'location_tag'       => trim($request->city_province) . ' (Outside Magalang)',
            'barangay'           => trim($request->worksite_location) . ', ' . trim($request->city_province),
            'preferred_schedule' => trim($request->schedule),
            'date_posted'        => now()->toDateString(),
            'status'             => 'Pending',
            'applicant_username' => null,
        ]);

        \App\Models\AuditLog::log(
            'REGIONAL_JOB_POSTED',
            "PESO Staff posted regional job opening outside Magalang: '{$job->title}' in {$request->city_province} ({$request->vacancies} worker slots).",
            Session::get('user_name', 'PESO Staff'),
            'PESO Staff',
            Session::get('user_id')
        );

        return back()->with('success', "Matagumpay na nai-post ang job opening sa labas ng Magalang: '{$job->title}' sa {$request->city_province}! Makikita na ito ng mga rehistradong Skilled Workers.");
    }

    public function resolveComplaint(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->status = 'Resolved';
        $complaint->resolution_decision = $request->input('resolution_decision', 'Amicable Settlement / Cleared');
        $complaint->sanction_status = $request->input('sanction_status', 'None');
        $complaint->resolution_notes = $request->input('notes', 'Resolved amicably by PESO Staff mediation.');
        $complaint->resolved_at = now();
        $complaint->save();

        \App\Models\AuditLog::log(
            'COMPLAINT_RESOLVED',
            "Incident complaint #{$complaint->complaint_id} resolved by PESO Staff mediation.",
            Session::get('user_name', 'PESO Staff'),
            'PESO Staff',
            Session::get('user_id')
        );

        return back()->with('success', "Incident complaint #{$complaint->complaint_id} marked as RESOLVED.");
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('peso_staff.user_management', compact('users'));
    }
}
