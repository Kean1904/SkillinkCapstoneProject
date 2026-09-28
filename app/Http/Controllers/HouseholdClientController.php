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
use Illuminate\Support\Facades\Log;

class HouseholdClientController extends Controller
{
    private function getCurrentUser()
    {
        $username = Session::get('user_name');
        return User::where('name', $username)->first()
            ?? User::whereIn('role', ['household client', 'household_client', 'residential'])->first()
            ?? new User(['name' => 'Testing 1', 'first_name' => 'Khane Hendrix', 'last_name' => 'Torres']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $availableWorkers = User::where('role', 'skilled worker')->count();
        $postedJobs = JobPost::count();

        $workersQuery = User::where('role', 'skilled worker');
        if (!empty($search)) {
            $workersQuery->where(function($q) use ($search) {
                $q->where('skills', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        $workersList = $workersQuery->latest()->get();
        return view('dashboard.HouseholdClient', compact('availableWorkers', 'postedJobs', 'workersList', 'search'));
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

        return view('household_client.hiring_history', compact('user', 'activeBookings', 'completedBookings', 'bookings'));
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'workerUsername' => 'required|string',
            'ratingStars' => 'required|integer|min:1|max:5',
        ]);

        $user = $this->getCurrentUser();
        $worker = User::where('name', $request->workerUsername)->first();

        Review::create([
            'booking_id' => $request->bookingId ?? 1,
            'client_id' => $user->user_id ?? 1,
            'worker_id' => $worker ? $worker->user_id : 1,
            'rating' => $request->ratingStars,
            'comment' => $request->reviewText ?? '',
            'created_at' => now(),
        ]);

        if ($worker) {
            $avg = Review::where('worker_id', $worker->user_id)->avg('rating');
            $worker->rating = round($avg, 1);
            $worker->save();
        }

        return back()->with('success', 'Your review has been submitted to the community!');
    }

    public function submitComplaint(Request $request)
    {
        $request->validate([
            'complaintType' => 'required|string',
            'description' => 'required|string|min:5',
        ]);

        $user = $this->getCurrentUser();

        $complaint = Complaint::create([
            'booking_id' => $request->bookingId ?? 1,
            'complainant_id' => $user->user_id ?? 1,
            'complaint_type' => $request->complaintType,
            'description' => $request->description,
            'status' => 'Pending',
            'created_at' => now(),
        ]);

        \App\Models\AuditLog::log(
            'COMPLAINT_FILED',
            "Household Client {$user->name} filed formal complaint (#CMP-{$complaint->complaint_id}) regarding '{$complaint->complaint_type}' against {$request->respondentUsername}.",
            $user->name,
            'Household Client',
            $user->user_id
        );

        return back()->with('success', 'Your grievance has been lodged with the PESO Mediation Officer.');
    }

    public function savedWorkers()
    {
        $user = $this->getCurrentUser();
        $workers = User::where('role', 'skilled worker')->latest()->get();
        return view('household_client.saved_workers', compact('user', 'workers'));
    }

    public function toggleSaveWorker($id)
    {
        return back()->with('success', 'Worker preference bookmarked successfully!');
    }

    public function getWorkerBookedDates($username)
    {
        try {
            $bookedDates = Booking::where('worker_username', $username)
                ->whereIn('status', ['PENDING', 'ACCEPTED', 'CONFIRMED', 'ASSIGNED', 'IN_PROGRESS', 'IN PROGRESS'])
                ->whereNotNull('scheduled_date')
                ->where('scheduled_date', '!=', '')
                ->pluck('scheduled_date')
                ->map(function ($date) {
                    return trim(explode(' ', $date)[0]);
                })
                ->filter(function ($date) {
                    return !empty($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
                })
                ->unique()
                ->values();

            return response()->json([
                'success' => true,
                'worker_username' => $username,
                'booked_dates' => $bookedDates,
                'count' => count($bookedDates)
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getWorkerBookedDates for {$username}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'booked_dates' => [],
                'message' => 'Failed to retrieve availability.'
            ], 500);
        }
    }

    public function createBooking(Request $request)
    {
        $validated = $request->validate([
            'workerUsername' => 'required|string',
            'serviceCategory' => 'required|string',
            'taskDescription' => 'required|string',
            'scheduledDate' => 'required|date',
            'serviceAddress' => 'required|string',
            'barangay' => 'required|string',
        ]);

        $worker = User::where('name', $validated['workerUsername'])->first();

        // 🌟 RULE 1: STRICT AVAILABILITY CHECK
        $existingBooking = Booking::where('worker_username', $validated['workerUsername'])
            ->whereDate('scheduled_date', $validated['scheduledDate'])
            ->whereIn('status', ['PENDING', 'ACCEPTED', 'CONFIRMED', 'ASSIGNED', 'IN_PROGRESS', 'IN PROGRESS'])
            ->exists();

        if ($existingBooking) {
            return back()->withInput()->with('error', "Pinaalala: Hindi available ang skilled worker na si {$validated['workerUsername']} sa napiling petsa ({$validated['scheduledDate']}) dahil may existing confirmed booking na ito. Pumili ng ibang clickable na available date sa calendar.");
        }

        // 🌟 RULE 2: FIXED ESTIMATED COST
        $workerProfile = DB::table('skilled_workers')
            ->where('username', $validated['workerUsername'])
            ->first();

        $fixedBudget = '₱500.00';
        if ($workerProfile && !empty($workerProfile->service_rates)) {
            $rates = json_decode($workerProfile->service_rates, true);
            if (is_array($rates) && isset($rates[$validated['serviceCategory']]) && !empty($rates[$validated['serviceCategory']])) {
                $rawVal = preg_replace('/[^\d.]/', '', (string)$rates[$validated['serviceCategory']]);
                if (is_numeric($rawVal) && floatval($rawVal) > 0) {
                    $fixedBudget = '₱' . number_format(floatval($rawVal), 2);
                }
            }
        }

        if ($fixedBudget === '₱500.00' && $worker && !empty($worker->skills)) {
            $lowerCat = strtolower($validated['serviceCategory']);
            if (str_contains($lowerCat, 'plumb')) $fixedBudget = '₱450.00';
            elseif (str_contains($lowerCat, 'electr')) $fixedBudget = '₱600.00';
            elseif (str_contains($lowerCat, 'carpen')) $fixedBudget = '₱550.00';
            elseif (str_contains($lowerCat, 'paint')) $fixedBudget = '₱400.00';
            elseif (str_contains($lowerCat, 'aircon') || str_contains($lowerCat, 'refrig')) $fixedBudget = '₱750.00';
            elseif (str_contains($lowerCat, 'weld')) $fixedBudget = '₱500.00';
            elseif (str_contains($lowerCat, 'mason')) $fixedBudget = '₱500.00';
            elseif (str_contains($lowerCat, 'appliance')) $fixedBudget = '₱400.00';
        }

        $user = $this->getCurrentUser();
        $refNumber = 'SRV-' . strtoupper(substr(uniqid(), -6));

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
            'estimated_budget' => $fixedBudget,
            'scheduled_date' => $validated['scheduledDate'],
            'status' => 'PENDING',
        ]);

        \App\Models\AuditLog::log(
            'BOOKING_CREATED',
            "Household Client {$user->name} created booking {$refNumber} for {$booking->service_category} with worker {$booking->worker_name} ({$fixedBudget}).",
            $user->name,
            'Household Client',
            $user->user_id
        );

        return redirect()->route('household_client.hiring_history')->with('success', "Service booking request ({$refNumber}) submitted successfully with fixed rate {$fixedBudget}!");
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

        return view('household_client.job_posts', compact('user', 'activeJobs', 'completedJobs', 'jobs'));
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
        return view('household_client.profile', compact('user'));
    }

    public function settings()
    {
        $user = $this->getCurrentUser();
        return view('household_client.settings', compact('user'));
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
