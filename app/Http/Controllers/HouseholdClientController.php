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
        if (\Illuminate\Support\Facades\Auth::check()) {
            return \Illuminate\Support\Facades\Auth::user();
        }
        $username = Session::get('user_name');
        $userId = Session::get('user_id');
        return User::where('name', $username)
            ->orWhere('user_id', $userId)
            ->first()
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
        $user = $this->getCurrentUser();
        $savedWorkerIds = DB::table('saved_workers')
            ->where('user_id', $user->user_id)
            ->pluck('worker_id')
            ->toArray();

        return view('dashboard.HouseholdClient', compact('user', 'availableWorkers', 'postedJobs', 'workersList', 'search', 'savedWorkerIds'));
    }

    public function hiringHistory()
    {
        $user = $this->getCurrentUser();

        // 1. Pending Job Applications from Skilled Workers awaiting Client Confirmation
        $pendingApplications = JobPost::where(function ($q) use ($user) {
                $q->where('client_id', $user->user_id)
                  ->orWhere('posted_by', $user->name)
                  ->orWhereRaw('LOWER(posted_by) = ?', [strtolower($user->name)]);
            })
            ->whereNotNull('applicant_username')
            ->where('applicant_username', '!=', '')
            ->whereIn('status', ['Applied', 'Pending Confirmation', 'APPLIED'])
            ->latest('updated_at')
            ->get();

        foreach ($pendingApplications as $app) {
            $app->applicant_worker = User::where('name', $app->applicant_username)
                ->orWhere('user_id', $app->applicant_username)
                ->orWhereRaw('LOWER(name) = ?', [strtolower($app->applicant_username)])
                ->first();
        }

        // 2. Active & Ongoing Service Bookings
        $activeBookings = Booking::where(function($q) use ($user) {
                $q->where('client_username', $user->name)
                  ->orWhereRaw('LOWER(client_username) = ?', [strtolower($user->name)]);
            })
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
            ->latest('created_at')
            ->get();

        // 3. Completed Bookings
        $completedBookings = Booking::where(function($q) use ($user) {
                $q->where('client_username', $user->name)
                  ->orWhereRaw('LOWER(client_username) = ?', [strtolower($user->name)]);
            })
            ->where('status', 'COMPLETED')
            ->latest('completion_date')
            ->latest('created_at')
            ->get();

        // Check if reviews or complaints already exist for these bookings
        $completedBookingIds = $completedBookings->pluck('booking_id')->filter()->toArray();
        $activeBookingIds = $activeBookings->pluck('booking_id')->filter()->toArray();
        $allTargetBookingIds = array_unique(array_filter(array_merge($completedBookingIds, $activeBookingIds)));

        $reviewedBookingIds = DB::table('rating_reviews')
            ->where(function ($q) use ($user, $allTargetBookingIds) {
                $q->where('client_id', $user->user_id)
                  ->orWhere('client_username', $user->name);
                if (!empty($allTargetBookingIds)) {
                    $q->orWhereIn('booking_id', $allTargetBookingIds);
                }
            })
            ->pluck('booking_id')
            ->map(fn($id) => (int)$id)
            ->toArray();

        $complaintBookingIds = DB::table('complaints')
            ->where(function ($q) use ($user, $allTargetBookingIds) {
                $q->where('submitted_by', $user->user_id)
                  ->orWhere('complainant_username', $user->name);
                if (!empty($allTargetBookingIds)) {
                    $q->orWhereIn('booking_id', $allTargetBookingIds);
                }
            })
            ->pluck('booking_id')
            ->map(fn($id) => (int)$id)
            ->toArray();

        foreach ($completedBookings as $comp) {
            $comp->has_review = in_array((int)$comp->booking_id, $reviewedBookingIds)
                || DB::table('rating_reviews')->where(function($q) use ($comp, $user) {
                    $q->where('booking_id', $comp->booking_id)
                      ->where(function($sq) use ($user, $comp) {
                          $sq->where('client_id', $user->user_id)
                             ->orWhere('client_username', $user->name)
                             ->orWhere('worker_username', $comp->worker_username);
                      });
                })->exists();

            $comp->has_complaint = in_array((int)$comp->booking_id, $complaintBookingIds)
                || DB::table('complaints')->where(function($q) use ($comp, $user) {
                    $q->where('booking_id', $comp->booking_id)
                      ->where(function($sq) use ($user, $comp) {
                          $sq->where('submitted_by', $user->user_id)
                             ->orWhere('complainant_username', $user->name)
                             ->orWhere('respondent_username', $comp->worker_username);
                      });
                })->exists();
        }

        foreach ($activeBookings as $act) {
            $act->has_complaint = in_array((int)$act->booking_id, $complaintBookingIds)
                || DB::table('complaints')->where(function($q) use ($act, $user) {
                    $q->where('booking_id', $act->booking_id)
                      ->where(function($sq) use ($user, $act) {
                          $sq->where('submitted_by', $user->user_id)
                             ->orWhere('complainant_username', $user->name)
                             ->orWhere('respondent_username', $act->worker_username);
                      });
                })->exists();
        }

        $bookings = $activeBookings;

        return view('household_client.hiring_history', compact('user', 'pendingApplications', 'activeBookings', 'completedBookings', 'bookings'));
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'workerUsername' => 'required|string',
            'ratingStars' => 'required|integer|min:1|max:5',
        ]);

        $user = $this->getCurrentUser();
        $worker = User::where('name', $request->workerUsername)->first();

        // Find booking record
        $booking = null;
        if ($request->filled('bookingId')) {
            $booking = Booking::where('booking_id', $request->bookingId)
                ->orWhere('booking_reference', $request->bookingId)
                ->first();
        }

        $numericBookingId = $booking ? $booking->booking_id : (is_numeric($request->bookingId) ? (int)$request->bookingId : null);
        $bookingRef = $booking ? $booking->booking_reference : ($request->bookingId ?? 'N/A');

        // Prevent duplicate review for the same booking
        if ($numericBookingId) {
            $alreadyReviewed = DB::table('rating_reviews')
                ->where('booking_id', $numericBookingId)
                ->where(function($q) use ($user) {
                    $q->where('client_id', $user->user_id)
                      ->orWhere('client_username', $user->name);
                })
                ->exists();

            if ($alreadyReviewed) {
                return back()->with('warning', 'Already Submitted: You have already submitted a review and rating for this service booking.');
            }
        }

        Review::create([
            'booking_id' => $numericBookingId,
            'client_id' => $user->user_id ?? 1,
            'client_username' => $user->name,
            'worker_id' => $worker ? $worker->user_id : ($booking ? $booking->worker_id : 1),
            'worker_username' => $request->workerUsername,
            'rating_score' => $request->ratingStars,
            'review_text' => $request->reviewText ?? '',
            'created_at' => now(),
        ]);

        if ($worker) {
            $avg = DB::table('rating_reviews')->where('worker_username', $worker->name)->avg('rating_score');
            if ($avg) {
                $worker->rating = round($avg, 1);
                $worker->save();
            }
        }

        \App\Models\AuditLog::log(
            'REVIEW_SUBMITTED',
            "Household Client {$user->name} gave {$request->ratingStars}-star review to {$request->workerUsername} for booking #{$bookingRef}.",
            $user->name,
            'Household Client',
            $user->user_id
        );

        return back()->with('success', 'Your rating & review have been submitted successfully!');
    }

    public function submitComplaint(Request $request)
    {
        $request->validate([
            'complaintType' => 'required|string',
            'description' => 'required|string|min:5',
        ]);

        $user = $this->getCurrentUser();

        // Find booking record
        $booking = null;
        if ($request->filled('bookingId')) {
            $booking = Booking::where('booking_id', $request->bookingId)
                ->orWhere('booking_reference', $request->bookingId)
                ->first();
        }

        $numericBookingId = $booking ? $booking->booking_id : (is_numeric($request->bookingId) ? (int)$request->bookingId : null);
        $bookingRef = $booking ? $booking->booking_reference : ($request->bookingId ?? 'N/A');

        // Prevent duplicate complaint for the same booking
        if ($numericBookingId) {
            $alreadyComplained = DB::table('complaints')
                ->where('booking_id', $numericBookingId)
                ->where(function($q) use ($user) {
                    $q->where('submitted_by', $user->user_id)
                      ->orWhere('complainant_username', $user->name);
                })
                ->exists();

            if ($alreadyComplained) {
                return back()->with('warning', 'Already Submitted: You have already filed a grievance/complaint for this service booking.');
            }
        }

        $complaint = Complaint::create([
            'booking_id' => $numericBookingId,
            'submitted_by' => $user->user_id ?? 1,
            'complainant_username' => $user->name,
            'respondent_username' => $request->respondentUsername ?? ($booking ? $booking->worker_username : 'Unknown Worker'),
            'complaint_type' => $request->complaintType,
            'description' => $request->description,
            'status' => 'Pending',
            'created_at' => now(),
        ]);

        \App\Models\AuditLog::log(
            'COMPLAINT_FILED',
            "Household Client {$user->name} filed formal complaint (#CMP-{$complaint->complaint_id}) regarding '{$complaint->complaint_type}' against {$complaint->respondent_username} for booking #{$bookingRef}.",
            $user->name,
            'Household Client',
            $user->user_id
        );

        return back()->with('success', 'Your grievance has been lodged with the PESO Mediation Officer.');
    }

    public function savedWorkers()
    {
        $user = $this->getCurrentUser();
        
        // 🌟 Only fetch workers that this specific user has bookmarked
        $savedWorkerIds = DB::table('saved_workers')
            ->where('user_id', $user->user_id)
            ->pluck('worker_id')
            ->toArray();

        if (empty($savedWorkerIds)) {
            $workers = collect();
        } else {
            $workers = User::whereIn('user_id', $savedWorkerIds)
                ->where('role', 'skilled worker')
                ->latest()
                ->get();
        }

        return view('household_client.saved_workers', compact('user', 'workers'));
    }

    public function toggleSaveWorker($id)
    {
        $user = $this->getCurrentUser();
        
        $targetWorker = User::where('user_id', $id)
            ->orWhere('name', $id)
            ->first();

        if (!$targetWorker) {
            return back()->with('error', 'Skilled worker record not found.');
        }

        $workerId = $targetWorker->user_id;
        $workerName = $targetWorker->full_name ?: $targetWorker->name;

        $existing = DB::table('saved_workers')
            ->where('user_id', $user->user_id)
            ->where('worker_id', $workerId)
            ->first();

        if ($existing) {
            DB::table('saved_workers')
                ->where('user_id', $user->user_id)
                ->where('worker_id', $workerId)
                ->delete();

            \App\Models\AuditLog::log(
                'WORKER_UNBOOKMARKED',
                "Household Client {$user->name} removed skilled worker {$workerName} (@{$targetWorker->name}) from saved bookmarks.",
                $user->name,
                'Household Client',
                $user->user_id
            );

            return back()->with('success', "Matagumpay na natanggal si {$workerName} mula sa iyong saved workers list!");
        } else {
            DB::table('saved_workers')->insert([
                'user_id' => $user->user_id,
                'worker_id' => $workerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \App\Models\AuditLog::log(
                'WORKER_BOOKMARKED',
                "Household Client {$user->name} saved skilled worker {$workerName} (@{$targetWorker->name}) to bookmarks.",
                $user->name,
                'Household Client',
                $user->user_id
            );

            return back()->with('success', "Matagumpay na naidagdag si {$workerName} sa iyong saved workers list!");
        }
    }

    public function getWorkerBookedDates($username)
    {
        try {
            $worker = User::where('name', $username)
                ->orWhere('user_id', $username)
                ->orWhereRaw('LOWER(name) = ?', [strtolower($username)])
                ->orWhereRaw('CONCAT(first_name, " ", last_name) = ?', [$username])
                ->first();

            $bookedDates = Booking::where(function($q) use ($username, $worker) {
                    $q->where('worker_username', $username);
                    if ($worker) {
                        $q->orWhere('worker_username', $worker->name)
                          ->orWhere('worker_id', $worker->user_id);
                    }
                })
                ->whereIn('status', ['PENDING', 'ACCEPTED', 'CONFIRMED', 'ASSIGNED', 'IN_PROGRESS', 'IN PROGRESS'])
                ->whereNotNull('scheduled_date')
                ->where('scheduled_date', '!=', '')
                ->pluck('scheduled_date')
                ->map(function ($date) {
                    $raw = trim(explode('(', (string)$date)[0]);
                    $ts = strtotime($raw);
                    if ($ts !== false) {
                        return date('Y-m-d', $ts);
                    }
                    return trim(explode(' ', (string)$date)[0]);
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
            'scheduledDate' => 'required|string',
            'serviceAddress' => 'required|string',
            'barangay' => 'required|string',
        ]);

        $worker = User::where('name', $validated['workerUsername'])
            ->orWhere('user_id', $validated['workerUsername'])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($validated['workerUsername'])])
            ->orWhereRaw('CONCAT(first_name, " ", last_name) = ?', [$validated['workerUsername']])
            ->first();

        // 🌟 RULE 1: STRICT AVAILABILITY CHECK
        $parsedDate = null;
        if (!empty($validated['scheduledDate'])) {
            $rawDate = trim(explode('(', $validated['scheduledDate'])[0]);
            $ts = strtotime($rawDate);
            if ($ts !== false) {
                $parsedDate = date('Y-m-d', $ts);
            }
        }

        if ($parsedDate) {
            $existingBooking = Booking::where(function($q) use ($validated, $worker) {
                    $q->where('worker_username', $validated['workerUsername']);
                    if ($worker) {
                        $q->orWhere('worker_username', $worker->name)
                          ->orWhere('worker_id', $worker->user_id);
                    }
                })
                ->where(function($q) use ($parsedDate, $validated) {
                    $q->whereDate('scheduled_date', $parsedDate)
                      ->orWhere('scheduled_date', 'like', "%{$parsedDate}%");
                })
                ->whereIn('status', ['PENDING', 'ACCEPTED', 'CONFIRMED', 'ASSIGNED', 'IN_PROGRESS', 'IN PROGRESS'])
                ->exists();

            if ($existingBooking) {
                return back()->withInput()->with('error', "Pinaalala: Hindi available ang skilled worker na si {$validated['workerUsername']} sa napiling petsa ({$validated['scheduledDate']}) dahil may existing confirmed booking na ito. Pumili ng ibang clickable na available date sa calendar.");
            }
        }

        // 🌟 RULE 2: FIXED ESTIMATED COST
        $fixedBudget = '₱500.00';
        if ($worker && !empty($worker->service_rate)) {
            $fixedBudget = $worker->service_rate_display;
        } elseif ($worker && !empty($worker->skills)) {
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

        // 🌟 BILATERAL MATCHING: Merge client's open job need in this category
        $matchedClientJob = JobPost::where(function ($q) use ($user) {
                $q->where('client_id', $user->user_id)
                  ->orWhere('posted_by', $user->name);
            })
            ->whereNotIn('status', ['Completed', 'Cancelled', 'Matched', 'Accepted', 'In Progress', 'IN_PROGRESS'])
            ->where(function ($q) use ($validated) {
                $cat = strtolower($validated['serviceCategory']);
                $q->whereRaw('LOWER(category) = ?', [$cat])
                  ->orWhere('category', 'like', "%{$validated['serviceCategory']}%");
            })
            ->latest('created_at')
            ->first();

        if ($matchedClientJob) {
            $matchedClientJob->status = 'Matched';
            $matchedClientJob->applicant_username = $worker ? $worker->name : $validated['workerUsername'];
            $matchedClientJob->save();
        }

        // 🌟 BILATERAL MATCHING: Merge worker's open job offer in this category
        $matchedWorkerOffer = JobPost::where(function ($q) use ($worker, $validated) {
                if ($worker) {
                    $q->where('client_id', $worker->user_id)
                      ->orWhere('posted_by', $worker->name);
                } else {
                    $q->where('posted_by', $validated['workerUsername']);
                }
            })
            ->whereNotIn('status', ['Completed', 'Cancelled', 'Matched', 'Accepted', 'In Progress', 'IN_PROGRESS'])
            ->where(function ($q) use ($validated) {
                $cat = strtolower($validated['serviceCategory']);
                $q->whereRaw('LOWER(category) = ?', [$cat])
                  ->orWhere('category', 'like', "%{$validated['serviceCategory']}%");
            })
            ->latest('created_at')
            ->first();

        if ($matchedWorkerOffer) {
            $matchedWorkerOffer->status = 'Matched';
            $matchedWorkerOffer->save();
        }

        $booking = Booking::create([
            'booking_reference' => $refNumber,
            'request_id' => $matchedClientJob ? $matchedClientJob->request_id : 1,
            'worker_id' => $worker ? $worker->user_id : 1,
            'client_username' => $user->name,
            'worker_username' => $worker ? $worker->name : $validated['workerUsername'],
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

        $mergeAuditDetail = "";
        if ($matchedClientJob) $mergeAuditDetail .= " [Merged Client Job Need #{$matchedClientJob->request_id}]";
        if ($matchedWorkerOffer) $mergeAuditDetail .= " [Merged Worker Job Offer #{$matchedWorkerOffer->request_id}]";

        \App\Models\AuditLog::log(
            'BOOKING_CREATED',
            "Household Client {$user->name} created booking {$refNumber} for {$booking->service_category} with worker {$booking->worker_name} ({$fixedBudget}).{$mergeAuditDetail}",
            $user->name,
            'Household Client',
            $user->user_id
        );

        $successMsg = "Service booking request ({$refNumber}) submitted successfully with fixed rate {$fixedBudget}!";
        if ($matchedClientJob) {
            $successMsg .= " Ang iyong Job Need ('{$matchedClientJob->title}') ay awtomatikong na-merge at naalis sa active job feed.";
        }

        return redirect()->route('household_client.hiring_history')->with('success', $successMsg);
    }

    public function jobPosts()
    {
        $user = $this->getCurrentUser();
        $activeJobs = JobPost::where(function ($q) use ($user) {
            $q->where('client_id', $user->user_id)
              ->orWhere('posted_by', $user->name);
        })->whereNotIn('status', ['Completed', 'Cancelled', 'Matched', 'Accepted', 'In Progress', 'IN_PROGRESS'])
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
            'lot_number' => 'nullable|string|max:255',
            'barangay' => 'required|string',
        ]);

        $user = $this->getCurrentUser();
        $lotNumber = !empty($validated['lot_number']) ? trim($validated['lot_number']) : null;
        $locationTag = $lotNumber ? "{$lotNumber}, Brgy. {$validated['barangay']}" : "Brgy. {$validated['barangay']}";

        JobPost::create([
            'title' => $validated['title'],
            'client_id' => $user->user_id ?? 1,
            'posted_by' => $user->name,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'location_tag' => $locationTag,
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

    public function respondApplication(Request $request, $id)
    {
        $job = JobPost::findOrFail($id);
        $action = strtolower($request->input('action', 'accept'));
        $user = $this->getCurrentUser();

        $worker = User::where('name', $job->applicant_username)
            ->orWhere('user_id', $job->applicant_username)
            ->orWhereRaw('LOWER(name) = ?', [strtolower($job->applicant_username)])
            ->first();

        $workerName = $worker ? $worker->full_name : $job->applicant_username;

        if ($action === 'accept') {
            $job->status = 'Matched';
            $job->save();

            // 🌟 BILATERAL MATCHING: Merge worker's open job offer in this category
            if ($worker) {
                $matchedWorkerOffer = JobPost::where(function ($q) use ($worker) {
                        $q->where('client_id', $worker->user_id)
                          ->orWhere('posted_by', $worker->name);
                    })
                    ->whereNotIn('status', ['Completed', 'Cancelled', 'Matched', 'Accepted', 'In Progress', 'IN_PROGRESS'])
                    ->where(function ($q) use ($job) {
                        $cat = strtolower($job->category);
                        $q->whereRaw('LOWER(category) = ?', [$cat])
                          ->orWhere('category', 'like', "%{$job->category}%");
                    })
                    ->latest('created_at')
                    ->first();

                if ($matchedWorkerOffer) {
                    $matchedWorkerOffer->status = 'Matched';
                    $matchedWorkerOffer->save();
                }
            }

            // Create or sync into service_bookings so it appears in both client's and worker's tracking!
            $refNumber = 'SRV-' . strtoupper(substr(uniqid(), -6));
            $booking = Booking::create([
                'booking_reference' => $refNumber,
                'request_id' => $job->request_id,
                'worker_id' => $worker ? $worker->user_id : 1,
                'client_username' => $user->name,
                'worker_username' => $worker ? $worker->name : $job->applicant_username,
                'client_name' => $user->full_name,
                'worker_name' => $workerName,
                'service_category' => $job->category,
                'task_description' => $job->title . ': ' . $job->description,
                'service_address' => $job->location_tag ?? ('Brgy. ' . $job->barangay),
                'barangay' => $job->barangay,
                'estimated_budget' => '₱500.00',
                'scheduled_date' => $job->preferred_schedule ?: now()->toDateString(),
                'status' => 'ACCEPTED',
            ]);

            \App\Models\AuditLog::log(
                'APPLICATION_ACCEPTED',
                "Household Client {$user->name} accepted application of worker {$job->applicant_username} for job '{$job->title}'. Booking {$refNumber} created.",
                $user->name,
                'Household Client',
                $user->user_id
            );

            return back()->with('success', "Matagumpay mong tinanggap ang aplikasyon ni {$workerName} (@{$job->applicant_username})! Nagsimula na ang inyong aktibong booking ({$refNumber}).");
        } else {
            $rejectedApplicant = $job->applicant_username;
            $job->applicant_username = null;
            $job->status = 'Pending';
            $job->save();

            \App\Models\AuditLog::log(
                'APPLICATION_DECLINED',
                "Household Client {$user->name} declined application of worker {$rejectedApplicant} for job '{$job->title}'.",
                $user->name,
                'Household Client',
                $user->user_id
            );

            return back()->with('warning', "Tinanggihan ang aplikasyon ni {$rejectedApplicant}. Bukas muli ang job posting para sa ibang manggagawa mula sa Magalang.");
        }
    }
}
