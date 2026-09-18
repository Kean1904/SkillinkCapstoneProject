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
            ->orWhere('worker_id', 1)
            ->latest('created_at')
            ->get();

        $appliedJobs = JobPost::where('applicant_username', $worker->name)
            ->orWhereNotNull('applicant_username')
            ->latest('created_at')
            ->get();

        return view('skilled_worker.tracking_service', compact('worker', 'bookings', 'appliedJobs'));
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
