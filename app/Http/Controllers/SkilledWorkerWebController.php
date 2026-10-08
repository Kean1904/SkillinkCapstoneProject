<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\JobPost;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SkilledWorkerWebController extends Controller
{
    private function getCurrentWorker()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return \Illuminate\Support\Facades\Auth::user();
        }
        $username = Session::get('user_name');
        $userId = Session::get('user_id');
        return User::where('name', $username)
            ->orWhere('user_id', $userId)
            ->first() 
            ?? User::where('role', 'skilled worker')->first()
            ?? new User(['name' => 'juan_plumber', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz']);
    }

    public function trackingService()
    {
        $worker = $this->getCurrentWorker();
        $activeBookings = Booking::where(function($q) use ($worker) {
                $q->where('worker_username', $worker->name)
                  ->orWhere('worker_id', $worker->user_id)
                  ->orWhereRaw('LOWER(worker_username) = ?', [strtolower($worker->name)])
                  ->orWhere('worker_name', $worker->full_name);
            })
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED', 'REJECTED', 'DECLINED'])
            ->latest('created_at')
            ->get();

        $completedBookings = Booking::where(function($q) use ($worker) {
                $q->where('worker_username', $worker->name)
                  ->orWhere('worker_id', $worker->user_id)
                  ->orWhereRaw('LOWER(worker_username) = ?', [strtolower($worker->name)])
                  ->orWhere('worker_name', $worker->full_name);
            })
            ->where('status', 'COMPLETED')
            ->latest('completion_date')
            ->latest('created_at')
            ->get();

        // Check if complaints already exist for these completed bookings
        $completedBookingIds = $completedBookings->pluck('booking_id')->filter()->toArray();
        $complaintBookingIds = DB::table('complaints')
            ->where(function ($q) use ($worker, $completedBookingIds) {
                $q->where('submitted_by', $worker->user_id)
                  ->orWhere('complainant_username', $worker->name);
                if (!empty($completedBookingIds)) {
                    $q->orWhereIn('booking_id', $completedBookingIds);
                }
            })
            ->pluck('booking_id')
            ->map(fn($id) => (int)$id)
            ->toArray();

        foreach ($completedBookings as $comp) {
            $comp->has_complaint = in_array((int)$comp->booking_id, $complaintBookingIds)
                || DB::table('complaints')->where('booking_id', $comp->booking_id)
                    ->where(function($sq) use ($worker) {
                        $sq->where('submitted_by', $worker->user_id)
                           ->orWhere('complainant_username', $worker->name);
                    })->exists();
        }

        $activeAppliedJobs = JobPost::where(function($q) use ($worker) {
                $q->where('applicant_username', $worker->name)
                  ->orWhereRaw('LOWER(applicant_username) = ?', [strtolower($worker->name)]);
            })
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->latest('created_at')
            ->get();

        $completedAppliedJobs = JobPost::where('applicant_username', $worker->name)
            ->where('status', 'Completed')
            ->latest('created_at')
            ->get();

        // Pass both for backward compatibility and specialized rendering
        $bookings = $activeBookings;
        $appliedJobs = $activeAppliedJobs;

        return view('skilled_worker.tracking_service', compact(
            'worker', 
            'activeBookings', 
            'completedBookings', 
            'activeAppliedJobs', 
            'completedAppliedJobs', 
            'bookings', 
            'appliedJobs'
        ));
    }

    public function submitComplaint(Request $request)
    {
        $request->validate([
            'complaintType' => 'required|string',
            'description' => 'required|string|min:5',
            'bookingId' => 'required',
        ]);

        $worker = $this->getCurrentWorker();

        $booking = Booking::where('booking_id', $request->bookingId)
            ->orWhere('booking_reference', $request->bookingId)
            ->first();

        $numericBookingId = $booking ? $booking->booking_id : (is_numeric($request->bookingId) ? (int)$request->bookingId : null);
        $bookingRef = $booking ? $booking->booking_reference : ($request->bookingId ?? 'N/A');

        // Prevent duplicate complaint for the same booking by this worker
        if ($numericBookingId) {
            $alreadyComplained = DB::table('complaints')
                ->where('booking_id', $numericBookingId)
                ->where(function($q) use ($worker) {
                    $q->where('submitted_by', $worker->user_id)
                      ->orWhere('complainant_username', $worker->name);
                })
                ->exists();

            if ($alreadyComplained) {
                return back()->with('warning', 'Already Submitted: You have already filed a grievance/complaint for this completed service.');
            }
        }

        // Handle Evidence / Proof Upload (Images & Videos up to 5 files)
        $uploadedEvidence = [];
        if ($request->hasFile('evidence_files')) {
            $files = $request->file('evidence_files');
            if (!is_array($files)) {
                $files = [$files];
            }
            $files = array_slice($files, 0, 5); // Limit of 5 media items

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $ext = strtolower($file->getClientOriginalExtension());
                    $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', '3gp', 'ogg']);
                    $fileName = 'comp_' . time() . '_' . uniqid() . '.' . $ext;
                    $file->move(public_path('uploads/complaints'), $fileName);
                    $uploadedEvidence[] = [
                        'path' => 'uploads/complaints/' . $fileName,
                        'name' => $file->getClientOriginalName(),
                        'type' => $isVideo ? 'video' : 'image',
                        'size' => filesize(public_path('uploads/complaints/' . $fileName)),
                    ];
                }
            }
        }

        $complaintType = $request->complaintType;
        $otherCategory = null;
        if (in_array(trim($complaintType), ['Others', 'Other Grievances'])) {
            $otherCategory = trim($request->input('otherCategory', ''));
        }

        $complaint = Complaint::create([
            'booking_id' => $numericBookingId,
            'submitted_by' => $worker->user_id ?? 1,
            'complainant_username' => $worker->name,
            'respondent_username' => $request->respondentUsername ?? ($booking ? $booking->client_username : 'Household Client'),
            'complaint_type' => $complaintType,
            'other_category' => $otherCategory,
            'evidence_files' => !empty($uploadedEvidence) ? json_encode($uploadedEvidence) : null,
            'description' => $request->description,
            'status' => 'Pending',
            'created_at' => now(),
        ]);

        \App\Models\AuditLog::log(
            'COMPLAINT_FILED',
            "Skilled Worker {$worker->name} filed formal complaint (#CMP-{$complaint->complaint_id}) regarding '{$complaint->complaint_type}' against client {$complaint->respondent_username} for booking #{$bookingRef}.",
            $worker->name,
            'Skilled Worker',
            $worker->user_id
        );

        return back()->with('success', 'Your grievance has been successfully submitted to the PESO Mediation Officer!');
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

        return back()->with('success', "You have successfully applied for '{$job->title}'! Application status is now updated.");
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
            'service_rate' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $worker->first_name = $request->first_name;
        $worker->last_name = $request->last_name;
        $worker->contact_number = $request->contact_number;
        $worker->barangay = $request->barangay;
        $worker->address = $request->address;
        if ($request->filled('skills')) $worker->skills = $request->skills;
        if ($request->filled('service_rate')) $worker->service_rate = $request->service_rate;
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

        \App\Models\AuditLog::log(
            'BOOKING_STATUS_UPDATE',
            "Booking {$booking->booking_reference} status updated to {$status}.",
            Session::get('user_name', 'Skilled Worker'),
            'Skilled Worker',
            Session::get('user_id')
        );

        return back()->with('success', "Booking {$booking->booking_reference} status successfully updated to {$status}!");
    }

    public function myServices()
    {
        $worker = $this->getCurrentWorker();
        $myJobOffers = JobPost::where(function ($q) use ($worker) {
            $q->where('posted_by', $worker->name)
              ->orWhere('client_id', $worker->user_id);
        })->whereNotIn('status', ['Completed', 'Cancelled', 'Matched', 'Accepted', 'In Progress', 'IN_PROGRESS'])
          ->latest('created_at')
          ->get();

        return view('skilled_worker.my_services', compact('worker', 'myJobOffers'));
    }

    public function createJobOffer(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'barangay' => 'required|string|max:100',
            'estimated_rate' => 'nullable|string|max:100',
        ]);

        $worker = $this->getCurrentWorker();

        $rateInfo = !empty($validated['estimated_rate']) ? " (Service Rate: " . $validated['estimated_rate'] . ")" : "";

        JobPost::create([
            'title' => $validated['title'],
            'client_id' => $worker->user_id ?? 1,
            'posted_by' => $worker->name,
            'category' => $validated['category'],
            'description' => $validated['description'] . $rateInfo,
            'location_tag' => $validated['barangay'],
            'barangay' => $validated['barangay'],
            'preferred_schedule' => 'Available for Booking',
            'date_posted' => now()->toDateString(),
            'status' => 'Pending',
            'applicant_username' => null,
        ]);

        \App\Models\AuditLog::log(
            'JOB_OFFER_CREATED',
            "Worker {$worker->name} posted new service offer: {$validated['title']} ({$validated['category']}).",
            $worker->name,
            'Skilled Worker',
            $worker->user_id
        );

        return back()->with('success', 'Your service job offer has been successfully published! It is now live across the Magalang portal.');
    }

    public function updateServices(Request $request)
    {
        $worker = $this->getCurrentWorker();
        $worker->skills = $request->input('skills', $worker->skills);
        $worker->certificate_proof = $request->input('certificate_proof', $worker->certificate_proof);
        $worker->save();

        \App\Models\AuditLog::log(
            'SKILLS_UPDATED',
            "Worker {$worker->name} updated registered trade skills to: {$worker->skills}.",
            $worker->name,
            'Skilled Worker',
            $worker->user_id
        );

        return back()->with('success', 'Your skills and services have been updated successfully!');
    }

    public function submitApplication(Request $request)
    {
        $worker = $this->getCurrentWorker();

        // Check if no file is attached for credentials
        if (!$request->hasFile('certificate_file') && !$request->hasFile('valid_id_file')) {
            return back()->with('error', 'You dont have attach file submitted')->withInput();
        }

        $request->validate([
            'certificate_proof' => 'required|string|max:255',
            'certificate_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
            'valid_id_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
            'skills' => 'nullable|string|max:255',
            'service_rate' => 'nullable|string|max:50',
        ]);

        $worker->certificate_proof = $request->certificate_proof;
        if ($request->filled('skills')) $worker->skills = $request->skills;
        if ($request->filled('service_rate')) $worker->service_rate = $request->service_rate;

        // Upload certificate file
        if ($request->hasFile('certificate_file')) {
            $certFile = $request->file('certificate_file');
            $certName = 'cert_' . $worker->user_id . '_' . time() . '.' . $certFile->getClientOriginalExtension();
            $destPath = public_path('uploads/certificates');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }
            $certFile->move($destPath, $certName);
            $worker->certificate_file = 'uploads/certificates/' . $certName;
        }

        // Upload valid ID file
        if ($request->hasFile('valid_id_file')) {
            $idFile = $request->file('valid_id_file');
            $idName = 'valid_id_' . $worker->user_id . '_' . time() . '.' . $idFile->getClientOriginalExtension();
            $destPath = public_path('uploads/valid_ids');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }
            $idFile->move($destPath, $idName);
            $worker->valid_id_proof = 'uploads/valid_ids/' . $idName;
        }

        // Accreditation application state: Pending PESO Staff Review
        $worker->is_verified = false;
        $worker->verification_status = 'pending';
        $worker->rejection_reason = null;
        $worker->save();

        \App\Models\AuditLog::log(
            'CREDENTIALS_SUBMISSION',
            "Worker {$worker->name} submitted/resubmitted credentials (TESDA / Valid ID) for PESO Accreditation.",
            $worker->name,
            'Skilled Worker',
            $worker->user_id
        );

        return back()->with('success', 'Your credentials have been submitted/resubmitted successfully! PESO Staff will verify your updated TESDA certificate and documents.');
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

    public function declinedBookings()
    {
        $worker = $this->getCurrentWorker();
        $declinedBookings = Booking::where(function($q) use ($worker) {
                $q->where('worker_username', $worker->name)
                  ->orWhere('worker_id', $worker->user_id)
                  ->orWhereRaw('LOWER(worker_username) = ?', [strtolower($worker->name)])
                  ->orWhere('worker_name', $worker->full_name);
            })
            ->whereIn('status', ['DECLINED', 'REJECTED', 'CANCELLED'])
            ->latest('updated_at')
            ->get();

        return view('skilled_worker.declined_bookings', compact('worker', 'declinedBookings'));
    }
}
