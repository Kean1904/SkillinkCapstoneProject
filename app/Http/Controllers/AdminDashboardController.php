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

        $auditLogsCount = User::count() + Booking::count() + Complaint::count();

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
        return view('admin.job_categories');
    }

    public function auditLogs()
    {
        return view('admin.audit_logs');
    }

    public function profile()
    {
        $user = $this->getCurrentAdmin();
        return view('admin.profile', compact('user'));
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
}
