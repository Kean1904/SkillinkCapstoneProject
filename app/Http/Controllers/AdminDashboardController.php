<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobPost;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminDashboardController extends Controller
{
    private function getCurrentAdmin()
    {
        $username = Session::get('user_name');
        return User::where('name', $username)->first()
            ?? User::where('role', 'admin')->first()
            ?? new User(['name' => 'admin@Admin', 'first_name' => 'Municipal', 'last_name' => 'Administrator']);
    }

    public function index(Request $request)
    {
        $numberOfJobs = JobPost::count();
        $doneJobs = Booking::where('status', 'COMPLETED')->count();
        $skilledWorkers = User::where('role', 'skilled worker')->count();
        $residential = User::where(function($q) {
            $q->where('role', 'residential')->orWhere('role', 'like', '%household%')->orWhere('role', 'like', '%client%');
        })->count();
        $staffCount = User::where('role', 'peso staff')->count();
        $adminCount = User::where('role', 'admin')->count();
        $complaints = Complaint::count();
        $reports = 0;

        $selectedRole = strtolower($request->query('role', 'all'));
        $search = strtolower($request->query('search', ''));

        $query = User::query()->latest('created_at');

        if ($selectedRole !== 'all') {
            if ($selectedRole === 'skilled') {
                $query->where('role', 'skilled worker');
            } elseif ($selectedRole === 'residential' || $selectedRole === 'household') {
                $query->where(function($q) {
                    $q->where('role', 'residential')->orWhere('role', 'like', '%household%')->orWhere('role', 'like', '%client%');
                });
            } elseif ($selectedRole === 'staff') {
                $query->where('role', 'peso staff');
            } elseif ($selectedRole === 'admin') {
                $query->where('role', 'admin');
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%")
                  ->orWhere('skills', 'like', "%{$search}%");
            });
        }

        $topCategories = JobPost::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->take(3)
            ->get();

        $topWorkers = User::where('role', 'skilled worker')
            ->where('is_verified', 1)
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        $auditLogsCount = \App\Models\AuditLog::enforceBounds();

        return view('dashboard.Admin', compact(
            'numberOfJobs',
            'doneJobs',
            'skilledWorkers',
            'residential',
            'staffCount',
            'adminCount',
            'complaints',
            'reports',
            'topCategories',
            'topWorkers',
            'auditLogsCount'
        ));
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('admin.user_management', compact('users'));
    }

    public function staff()
    {
        $staffMembers = User::where('role', 'peso staff')->latest()->get();
        return view('admin.staff_management', compact('staffMembers'));
    }

    public function categories()
    {
        $categoriesMap = \App\Services\CategoryService::getCategoriesMap();
        return view('admin.job_categories', compact('categoriesMap'));
    }

    public function auditLogs(Request $request)
    {
        \App\Models\AuditLog::enforceBounds();

        $search = $request->input('search');
        $query = \App\Models\AuditLog::latest('log_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('actor_name', 'like', "%{$search}%")
                  ->orWhere('actor_role', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Paginate by 10 items per page with query string preserved
        $logs = $query->paginate(10)->withQueryString();

        return view('admin.audit_logs', compact('logs', 'search'));
    }

    public function resetAuditLogs()
    {
        $count = \App\Models\AuditLog::resetLogs();
        return back()->with('success', "Audit trail logs successfully reset to baseline ({$count} events)!");
    }

    public function profile()
    {
        $user = $this->getCurrentAdmin();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $this->getCurrentAdmin();

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

        return back()->with('success', 'Master Administrator profile details updated successfully!');
    }

    public function settings()
    {
        $user = $this->getCurrentAdmin();
        return view('admin.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user = $this->getCurrentAdmin();
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Master Administrator password updated successfully!');
    }

    public function complaints()
    {
        $complaints = Complaint::latest('complaint_id')->get();
        return view('admin.complaints', compact('complaints'));
    }

    public function resolveComplaint(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->status = 'Resolved';
        $complaint->resolution_decision = $request->input('resolution_decision', 'Amicable Settlement / Cleared');
        $complaint->sanction_status = $request->input('sanction_status', 'None');
        $complaint->resolution_notes = $request->input('notes', 'Resolved by Municipal Administrator mediation.');
        $complaint->resolved_at = now();
        $complaint->save();

        \App\Models\AuditLog::log(
            'COMPLAINT_RESOLVED',
            "Administrator resolved grievance #CMP-{$complaint->complaint_id} via executive mediation.",
            Session::get('user_name', 'Administrator'),
            'Administrator',
            Session::get('user_id')
        );

        return back()->with('success', "Grievance #CMP-{$complaint->complaint_id} marked as RESOLVED (Case Close)!");
    }

    public function announcements()
    {
        $jobs = JobPost::latest()->get();
        $announcements = \App\Models\Announcement::latest()->get();
        return view('admin.announcements', compact('jobs', 'announcements'));
    }

    public function broadcastAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'message' => 'required|string',
            'target_audience' => 'required|string|in:all,skilled_worker,residential',
        ]);

        \App\Models\Announcement::create([
            'title' => trim($request->title),
            'category' => trim($request->category),
            'message' => trim($request->message),
            'target_audience' => trim($request->target_audience),
            'posted_by' => Session::get('user_name', 'Municipal Administrator'),
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
            "Administrator broadcasted municipal announcement: '{$request->title}' ({$request->category}) for {$request->target_audience} to {$recipients->count()} recipients.",
            Session::get('user_name', 'Administrator'),
            'Administrator',
            Session::get('user_id')
        );

        $targetText = $request->target_audience === 'skilled_worker' ? 'Skilled Workers lamang' : ($request->target_audience === 'residential' ? 'Household Clients lamang' : 'lahat ng rehistradong gumagamit');

        return back()->with('success', "Municipal announcement ('{$request->title}') posted successfully para sa {$targetText} at broadcasted sa {$recipients->count()} accounts!");
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
            "Administrator posted job opportunity outside Magalang: '{$job->title}' in {$request->city_province} ({$request->vacancies} worker slots).",
            Session::get('user_name', 'Administrator'),
            Session::get('role', 'Administrator'),
            Session::get('user_id')
        );

        return back()->with('success', "Matagumpay na nai-post ang trabaho sa labas ng Magalang: '{$job->title}' sa {$request->city_province}! Makikita na ito ng mga Skilled Workers.");
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

    public function getTesdaReportData()
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

        // Trade / Skill breakdown among uncertified workers (services to request from TESDA)
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

        // Barangay breakdown for uncertified workers
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

        return compact(
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
        );
    }

    public function tesdaReports()
    {
        $data = $this->getTesdaReportData();
        return view('admin.dole_reports', $data);
    }

    public function doleReports()
    {
        return $this->tesdaReports();
    }

    public function tesdaReportsLiveData()
    {
        $data = $this->getTesdaReportData();
        return response()->json([
            'success'               => true,
            'timestamp'             => now()->toIso8601String(),
            'totalWorkers'          => $data['totalWorkers'],
            'certifiedCount'        => $data['certifiedCount'],
            'uncertifiedCount'      => $data['uncertifiedCount'],
            'certificationRate'     => $data['certificationRate'],
            'prioritySkillsCount'   => $data['prioritySkillsCount'],
            'tradesNeedingTraining' => $data['tradesNeedingTraining'],
            'uncertifiedByBarangay' => $data['uncertifiedByBarangay'],
            'qualificationsNeeded'  => $data['qualificationsNeeded'],
            'uncertifiedWorkers'    => $data['uncertifiedWorkers']->map(function($w) {
                return [
                    'user_id'           => $w->user_id,
                    'name'              => $w->name,
                    'full_name'         => trim(($w->first_name ?? '') . ' ' . ($w->last_name ?? '')) ?: $w->name,
                    'contact'           => $w->contact_number ?: $w->email ?: 'N/A',
                    'skills'            => $w->skills ?: 'General Handyman',
                    'barangay'          => $w->barangay ?: 'Magalang',
                    'rating'            => $w->rating ? number_format((float)$w->rating, 1) : '5.0',
                    'certificate_proof' => $w->certificate_proof ?: 'None',
                    'has_nc'            => false
                ];
            }),
        ]);
    }
}
