<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SkilledWorkerWebController extends Controller
{
    private function getCurrentWorker()
    {
        $username = Session::get('user_name');
        return User::where('name', $username)->first() 
            ?? User::where('role', 'skilled worker')->first()
            ?? new User(['name' => 'juan_plumber', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz']);
    }

    public function trackingService()
    {
        $worker = $this->getCurrentWorker();
        $bookings = Booking::where('worker_username', $worker->name)
            ->latest('created_at')
            ->get();

        $appliedJobs = JobPost::where('applicant_username', $worker->name)
            ->latest('created_at')
            ->get();

        return view('skilled_worker.tracking_service', compact('worker', 'bookings', 'appliedJobs'));
    }

    public function applyJob(Request $request, $id)
    {
        $worker = $this->getCurrentWorker();
        $job = JobPost::findOrFail($id);

        if ($job->status === 'Completed') {
            return back()->with('error', 'This job posting has already been completed.');
        }

        $job->applicant_username = $worker->name;
        $job->status = 'Applied';
        $job->save();

        return back()->with('success', "You have successfully applied for '{$job->title}'! The client and PESO Magalang have been notified.");
    }

    public function updateProfile(Request $request)
    {
        $worker = $this->getCurrentWorker();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:50',
            'barangay' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:255',
            'certificate_proof' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:15|max:120',
            'gender' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $worker->first_name = $request->first_name;
        $worker->last_name = $request->last_name;
        $worker->contact_number = $request->contact_number;
        $worker->barangay = $request->barangay;
        $worker->address = $request->address;
        if ($request->filled('skills')) $worker->skills = $request->skills;
        if ($request->filled('certificate_proof')) $worker->certificate_proof = $request->certificate_proof;
        if ($request->filled('age')) $worker->age = $request->age;
        if ($request->filled('gender')) $worker->gender = $request->gender;

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = 'avatar_' . $worker->user_id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $destinationPath = public_path('uploads/avatars');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $avatar->move($destinationPath, $filename);
            $worker->profile_image_uri = 'uploads/avatars/' . $filename;
            Session::put('profile_image_uri', $worker->profile_image_uri);
        }

        $worker->save();
        Session::put('full_name', $worker->first_name . ' ' . $worker->last_name);

        return back()->with('success', 'Your worker profile details have been updated successfully!');
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::where('booking_id', $id)
            ->orWhere('booking_reference', $id)
            ->firstOrFail();

        $status = strtoupper($request->input('status', 'ACCEPTED'));
        $booking->status = $status;
        if ($status === 'COMPLETED') {
            $booking->completion_date = now()->toDateString();
        }
        $booking->save();

        return back()->with('success', "Booking {$booking->booking_reference} status successfully updated to {$status}!");
    }

    public function myServices()
    {
        $worker = $this->getCurrentWorker();
        return view('skilled_worker.my_services', compact('worker'));
    }

    public function updateServices(Request $request)
    {
        $worker = $this->getCurrentWorker();
        $worker->skills = $request->input('skills', $worker->skills);
        $worker->certificate_proof = $request->input('certificate_proof', $worker->certificate_proof);
        $worker->save();

        return back()->with('success', 'Your skills and services have been updated successfully!');
    }

    public function profile()
    {
        $worker = $this->getCurrentWorker();
        return view('skilled_worker.profile', compact('worker'));
    }

    public function settings()
    {
        $worker = $this->getCurrentWorker();
        return view('skilled_worker.settings', compact('worker'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $worker = $this->getCurrentWorker();
        $worker->password_hash = Hash::make($request->new_password);
        $worker->save();

        return back()->with('success', 'Your password has been changed successfully!');
    }
}
