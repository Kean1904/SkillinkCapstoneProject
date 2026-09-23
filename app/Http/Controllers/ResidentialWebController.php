<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\JobPost;
use App\Models\User;
use App\Models\Review;
use App\Models\Complaint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingAlertMail;
use App\Services\ResendMailService;

class ResidentialWebController extends Controller
{
    private function getCurrentUser()
    {
        $username = Session::get('user_name');
        return User::where('name', $username)->first()
            ?? User::where('role', 'residential')->first()
            ?? new User(['name' => 'Testing 1', 'first_name' => 'Khane Hendrix', 'last_name' => 'Torres']);
    }

    public function hiringHistory()
    {
        $user = $this->getCurrentUser();
        $activeBookings = Booking::where('client_username', $user->name)
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
            ->latest('created_at')
            ->get();

        $completedBookings = Booking::where('client_username', $user->name)
            ->where('status', 'COMPLETED')
            ->latest('completion_date')
            ->latest('created_at')
            ->get();

        $bookings = $activeBookings;

        return view('residential.hiring_history', compact('user', 'activeBookings', 'completedBookings', 'bookings'));
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'workerUsername' => 'required|string',
            'ratingStars' => 'required|integer|min:1|max:5',
        ]);

        $user = $this->getCurrentUser();
        $worker = User::where('name', $request->workerUsername)->first();
        $workerProfile = $worker ? DB::table('worker_profiles')->where('user_id', $worker->user_id)->first() : null;

        $booking = Booking::where('booking_reference', $request->bookingId)->first();

        Review::create([
            'booking_id' => $booking ? $booking->booking_id : null,
            'client_id' => $user->user_id ?? 1,
            'worker_id' => $workerProfile ? $workerProfile->worker_id : 1,
            'client_username' => $user->name,
            'worker_username' => $request->workerUsername,
            'rating_score' => $request->ratingStars,
            'review_text' => $request->reviewText ?? '',
            'created_at' => now(),
        ]);

        if ($worker) {
            $avg = Review::where('worker_username', $worker->name)->avg('rating_score') ?: $request->ratingStars;
            $worker->rating = round($avg, 2);
            $worker->save();
        }

        return back()->with('success', 'Thank you! Your rating and feedback has been submitted successfully.');
    }

    public function submitComplaint(Request $request)
    {
        $request->validate([
            'respondentUsername' => 'required|string',
            'complaintType' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = $this->getCurrentUser();
        $booking = Booking::where('booking_reference', $request->bookingId)->first();

        Complaint::create([
            'booking_id' => $booking ? $booking->booking_id : null,
            'submitted_by' => $user->user_id ?? 1,
            'complainant_username' => $user->name,
            'respondent_username' => $request->respondentUsername,
            'complaint_type' => $request->complaintType,
            'description' => $request->description,
            'status' => 'Pending Investigation',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Official complaint filed successfully! PESO Magalang will investigate.');
    }

    public function savedWorkers()
    {
        $user = $this->getCurrentUser();
        $savedWorkerIds = DB::table('saved_workers')->where('user_id', $user->user_id)->pluck('worker_id');
        $workers = User::whereIn('user_id', $savedWorkerIds)->get();
        return view('residential.saved_workers', compact('user', 'workers'));
    }

    public function toggleSaveWorker(Request $request, $workerId)
    {
        $user = $this->getCurrentUser();
        $existing = DB::table('saved_workers')
            ->where('user_id', $user->user_id)
            ->where('worker_id', $workerId)
            ->first();

        if ($existing) {
            DB::table('saved_workers')
                ->where('user_id', $user->user_id)
                ->where('worker_id', $workerId)
                ->delete();
            return back()->with('success', 'Worker removed from your saved bookmarks.');
        } else {
            DB::table('saved_workers')->insert([
                'user_id' => $user->user_id,
                'worker_id' => $workerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return back()->with('success', 'Worker saved to your bookmarks!');
        }
    }

    public function createBooking(Request $request)
    {
        $validated = $request->validate([
            'workerUsername' => 'required|string',
            'serviceCategory' => 'required|string',
            'taskDescription' => 'required|string',
            'serviceAddress' => 'required|string',
            'barangay' => 'required|string',
            'estimatedBudget' => 'required|string',
            'scheduledDate' => 'required|string',
        ]);

        $user = $this->getCurrentUser();
        $worker = User::where('name', $validated['workerUsername'])->first();
        $workerProfile = $worker ? DB::table('worker_profiles')->where('user_id', $worker->user_id)->first() : null;

        $refNumber = 'BK-' . rand(100000, 999999);

        $booking = Booking::create([
            'booking_reference' => $refNumber,
            'request_id' => 1,
            'worker_id' => $workerProfile ? $workerProfile->worker_id : 1,
            'client_username' => $user->name,
            'worker_username' => $validated['workerUsername'],
            'client_name' => $user->full_name,
            'worker_name' => $worker ? $worker->full_name : $validated['workerUsername'],
            'service_category' => $validated['serviceCategory'],
            'task_description' => $validated['taskDescription'],
            'service_address' => $validated['serviceAddress'],
            'barangay' => $validated['barangay'],
            'estimated_budget' => $validated['estimatedBudget'],
            'scheduled_date' => $validated['scheduledDate'],
            'status' => 'PENDING',
        ]);

        // 🌟 Dispatch Real Email Notification to Skilled Worker
        if ($worker && !empty($worker->email)) {
            ResendMailService::sendMailable($worker->email, new BookingAlertMail($booking));
        }

        return redirect()->route('residential.hiring_history')->with('success', "Service booking request ({$refNumber}) submitted successfully!");
    }

    public function jobPosts()
    {
        $user = $this->getCurrentUser();
        $activeJobs = JobPost::where(function ($q) use ($user) {
            $q->where('client_id', $user->user_id)
              ->orWhere('posted_by', $user->name);
        })->whereNotIn('status', ['Completed', 'Cancelled'])
          ->latest('created_at')
          ->get();

        $completedJobs = JobPost::where(function ($q) use ($user) {
            $q->where('client_id', $user->user_id)
              ->orWhere('posted_by', $user->name);
        })->where('status', 'Completed')
          ->latest('created_at')
          ->get();

        $jobs = $activeJobs;

        return view('residential.job_posts', compact('user', 'activeJobs', 'completedJobs', 'jobs'));
    }

    public function completeJob($id)
    {
        $user = $this->getCurrentUser();
        $job = JobPost::where('request_id', $id)
            ->where(function ($q) use ($user) {
                $q->where('client_id', $user->user_id)
                  ->orWhere('posted_by', $user->name);
            })->firstOrFail();

        $job->status = 'Completed';
        $job->save();

        return back()->with('success', "Job '{$job->title}' has been successfully marked as Completed and moved to history!");
    }

    public function updateProfile(Request $request)
    {
        $user = $this->getCurrentUser();

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

        return back()->with('success', 'Your profile details have been successfully updated!');
    }

    public function createJob(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'barangay' => 'required|string',
        ]);

        $user = $this->getCurrentUser();

        JobPost::create([
            'title' => $validated['title'],
            'client_id' => $user->user_id ?? 1,
            'posted_by' => $user->name,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'location_tag' => $validated['barangay'],
            'barangay' => $validated['barangay'],
            'preferred_schedule' => 'Flexible',
            'date_posted' => now()->toDateString(),
            'status' => 'Pending',
            'applicant_username' => null,
        ]);

        return back()->with('success', 'Your job requirement has been published successfully!');
    }

    public function profile()
    {
        $user = $this->getCurrentUser();
        return view('residential.profile', compact('user'));
    }

    public function settings()
    {
        $user = $this->getCurrentUser();
        return view('residential.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user = $this->getCurrentUser();
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Your password has been changed successfully!');
    }
}
