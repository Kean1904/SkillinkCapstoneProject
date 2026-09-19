<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobPost;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
        $availableJobs = JobPost::where('status', '!=', 'Completed')->count();
        $pendingJobs = JobPost::where('status', 'Pending')->count();
        $skilledWorkers = User::where('role', 'skilled worker')->count();
        $residential = User::where('role', 'residential')->count();
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
        $complaintsList = Complaint::latest()->get();

        return view('dashboard.PesoStaff', compact(
            'availableJobs',
            'pendingJobs',
            'skilledWorkers',
            'residential',
            'complaintsCount',
            'accreditationQueue',
            'verifiedWorkers',
            'jobsList',
            'complaintsList'
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
        return view('peso_staff.job_tracking', compact('jobs'));
    }

    public function complaints()
    {
        $complaints = Complaint::latest('complaint_id')->get();
        return view('peso_staff.complaints', compact('complaints'));
    }

    public function reports()
    {
        $totalWorkers = User::where('role', 'skilled worker')->count();
        $accreditedWorkers = User::where('role', 'skilled worker')->where('is_verified', true)->count();
        $totalBookings = Booking::count();
        $resolvedComplaints = Complaint::where('status', 'Resolved')->count();

        return view('peso_staff.reports', compact(
            'totalWorkers',
            'accreditedWorkers',
            'totalBookings',
            'resolvedComplaints'
        ));
    }

    public function announcements()
    {
        return view('peso_staff.announcements');
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

    public function accreditWorker(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->save();

        return back()->with('success', "Worker {$user->full_name} has been officially accredited by PESO Magalang!");
    }

    public function resolveComplaint(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->status = 'Resolved';
        $complaint->resolution_notes = $request->input('notes', 'Resolved amicably by PESO Staff mediation.');
        $complaint->resolved_at = now();
        $complaint->save();

        return back()->with('success', "Incident complaint #{$complaint->complaint_id} marked as RESOLVED.");
    }
}
