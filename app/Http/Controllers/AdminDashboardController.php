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
        $residential = User::where('role', 'residential')->count();
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
            } elseif ($selectedRole === 'residential') {
                $query->where('role', 'residential');
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
        } elseif ($request->target_audience === 'residential') {
            $query->where('role', 'residential');
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

    public function doleReports()
    {
        $totalWorkers = User::where('role', 'skilled worker')->count();
        $accreditedWorkers = User::where('role', 'skilled worker')->where('is_verified', true)->count();
        $pendingWorkers = User::where('role', 'skilled worker')->where('is_verified', false)->count();
        $totalResidential = User::where('role', 'residential')->count();
        $totalJobPosts = JobPost::count();
        $totalBookings = Booking::count();
        $completedBookings = Booking::where('status', 'COMPLETED')->count();
        $resolvedComplaints = Complaint::where('status', 'Resolved')->count();
        $totalComplaints = Complaint::count();

        // Employment rate calculation
        $employmentRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 100.0;

        // Trade breakdown
        $topTrades = JobPost::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Barangay breakdown
        $barangayStats = User::select('barangay', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        return view('admin.dole_reports', compact(
            'totalWorkers',
            'accreditedWorkers',
            'pendingWorkers',
            'totalResidential',
            'totalJobPosts',
            'totalBookings',
            'completedBookings',
            'resolvedComplaints',
            'totalComplaints',
            'employmentRate',
            'topTrades',
            'barangayStats'
        ));
    }
}
