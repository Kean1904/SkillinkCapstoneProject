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
        // 1. General TESDA recognized classifications in the Philippines & Magalang
        $tesdaBaseCategories = [
            'Plumbing Repair' => [
                'desc' => 'Pipe fitting, drainage, leakage, seals, water system installation and repair.',
                'icon' => 'fa-faucet-drip',
                'keywords' => ['plumb', 'pipe', 'drainage', 'tubo', 'water']
            ],
            'Electrical Installation' => [
                'desc' => 'Wiring, circuit breaker troubleshooting, outlets, lighting fixtures, panel boards.',
                'icon' => 'fa-bolt',
                'keywords' => ['electr', 'kuryente', 'wiring', 'breaker', 'light']
            ],
            'Carpentry & Roofing' => [
                'desc' => 'Furniture, ceilings, doors, cabinetry, roofing repair, framing, wooden structures.',
                'icon' => 'fa-hammer',
                'keywords' => ['carpen', 'wood', 'roof', 'karpintero', 'kisame', 'bubong']
            ],
            'Welding & Fabrication' => [
                'desc' => 'Steel gates, window grills, structural metalworks, SMAW arc welding.',
                'icon' => 'fa-fire',
                'keywords' => ['weld', 'bakal', 'grill', 'metal', 'steel', 'smaw']
            ],
            'Masonry & Construction' => [
                'desc' => 'Concrete hollow blocks, tiling, plastering, cement, wall repair, masonry works.',
                'icon' => 'fa-trowel-bricks',
                'keywords' => ['mason', 'tile', 'semento', 'concrete', 'plaster']
            ],
            'Appliance & Refrigeration Repair' => [
                'desc' => 'Air conditioning cleaning, refrigerator maintenance, washing machine repair.',
                'icon' => 'fa-tv',
                'keywords' => ['appliance', 'aircon', 'ref', 'fridge', 'washing']
            ],
            'IT & Computer Systems Servicing' => [
                'desc' => 'Hardware repair, networking, software diagnostics, IT technician services, CSS NC II.',
                'icon' => 'fa-laptop-code',
                'keywords' => ['it', 'computer', 'technician', 'laptop', 'hardware', 'software', 'network']
            ],
            'Automotive & Small Engine Servicing' => [
                'desc' => 'Motorcycle tuning, automobile engine maintenance, brake & electrical repairs.',
                'icon' => 'fa-wrench',
                'keywords' => ['auto', 'motor', 'mechanic', 'mekaniko', 'car', 'engine']
            ],
            'Housekeeping & Domestic Services' => [
                'desc' => 'Home deep cleaning, sanitation, laundry, housekeeping assistance, domestic work.',
                'icon' => 'fa-broom',
                'keywords' => ['housekeep', 'clean', 'domestic', 'linis', 'laundry']
            ],
            'Driving & Transport Services' => [
                'desc' => 'Light vehicle driving, passenger transport, truck logistics, heavy equipment.',
                'icon' => 'fa-truck-fast',
                'keywords' => ['driv', 'transport', 'deliver']
            ],
            'Bread & Pastry Production / Culinary' => [
                'desc' => 'Baking, pastry prep, catering assistance, food handling, culinary services.',
                'icon' => 'fa-bread-slice',
                'keywords' => ['bread', 'pastry', 'baking', 'cook', 'culinary', 'food']
            ],
            'Electronics & Mechatronics' => [
                'desc' => 'Electronic device repair, solar power wiring, PCB troubleshooting, mechatronics.',
                'icon' => 'fa-microchip',
                'keywords' => ['electronic', 'mechatronic', 'circuit', 'solar']
            ],
            'Painting & Surface Finishing' => [
                'desc' => 'Exterior & interior house painting, varnishing, waterproof coating, wall finishing.',
                'icon' => 'fa-paint-roller',
                'keywords' => ['paint', 'pintor', 'varnish']
            ],
        ];

        // 2. Query all skilled workers and their skills in Magalang
        $skilledWorkers = User::where('role', 'skilled worker')->get();
        $jobPosts = JobPost::all();

        $categoriesMap = [];

        // Seed with TESDA base categories
        foreach ($tesdaBaseCategories as $catName => $info) {
            $categoriesMap[$catName] = [
                'title' => $catName,
                'desc' => $info['desc'],
                'icon' => $info['icon'],
                'keywords' => $info['keywords'],
                'worker_count' => 0,
                'active_workers' => [],
                'job_count' => 0,
                'is_tesda_standard' => true,
            ];
        }

        // Map skilled workers into categories, and dynamically create new categories for novel skills
        foreach ($skilledWorkers as $worker) {
            $rawSkills = $worker->skills ?? 'General Handyman';
            $skillParts = array_map('trim', explode(',', $rawSkills));

            foreach ($skillParts as $skill) {
                if (empty($skill)) continue;

                $matchedCategory = null;
                $skillLower = strtolower($skill);

                // Check against existing categories keywords
                foreach ($categoriesMap as $catKey => $catData) {
                    foreach ($catData['keywords'] as $kw) {
                        if (str_contains($skillLower, $kw)) {
                            $matchedCategory = $catKey;
                            break 2;
                        }
                    }
                }

                // If not matched, dynamically create category based on the worker's entered skill
                if (!$matchedCategory) {
                    $catTitle = ucwords($skill);
                    if (!isset($categoriesMap[$catTitle])) {
                        $categoriesMap[$catTitle] = [
                            'title' => $catTitle,
                            'desc' => "Community trade category actively offered by registered skilled workers in Magalang.",
                            'icon' => 'fa-toolbox',
                            'keywords' => [strtolower($skill)],
                            'worker_count' => 0,
                            'active_workers' => [],
                            'job_count' => 0,
                            'is_tesda_standard' => false,
                        ];
                    }
                    $matchedCategory = $catTitle;
                }

                // Increment worker count & add worker reference
                $categoriesMap[$matchedCategory]['worker_count']++;
                $workerDisplayName = $worker->full_name . ' (' . ($worker->barangay ?? 'Magalang') . ')';
                if (!in_array($workerDisplayName, $categoriesMap[$matchedCategory]['active_workers'])) {
                    $categoriesMap[$matchedCategory]['active_workers'][] = $workerDisplayName;
                }
            }
        }

        // Also check JobPost categories
        foreach ($jobPosts as $job) {
            $cat = $job->category;
            if (isset($categoriesMap[$cat])) {
                $categoriesMap[$cat]['job_count']++;
            }
        }

        // Sort: Categories with active workers first, then alphabetical
        uasort($categoriesMap, function ($a, $b) {
            if ($a['worker_count'] === $b['worker_count']) {
                return strcmp($a['title'], $b['title']);
            }
            return $b['worker_count'] <=> $a['worker_count'];
        });

        return view('admin.job_categories', compact('categoriesMap'));
    }

    public function auditLogs()
    {
        \App\Models\AuditLog::enforceBounds();
        $logs = \App\Models\AuditLog::latest('log_id')->take(\App\Models\AuditLog::MAX_LOGS)->get();
        return view('admin.audit_logs', compact('logs'));
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
        return view('admin.announcements', compact('jobs'));
    }

    public function broadcastAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'message' => 'required|string',
            'target_audience' => 'required|string|in:all,skilled_worker,residential',
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
            "Administrator broadcasted municipal announcement: '{$request->title}' to {$recipients->count()} recipients.",
            Session::get('user_name', 'Administrator'),
            'Administrator',
            Session::get('user_id')
        );

        return back()->with('success', "Municipal announcement broadcasted successfully to {$recipients->count()} registered users!");
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
            'uncertifiedByBarangay'
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
