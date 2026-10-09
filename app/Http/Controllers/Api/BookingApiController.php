<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\JobPost;
use App\Models\User;
use App\Mail\BookingAlertMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BookingApiController extends Controller
{
    public function index(Request $request)
    {
        $username = $request->query('username');
        $query = Booking::query()->latest('created_at');

        if ($username) {
            $query->where(function ($q) use ($username) {
                $q->where('client_username', $username)
                  ->orWhere('worker_username', $username);
            });
        }

        $bookings = $query->get()->map(function ($b) {
            return [
                'id' => $b->booking_reference ?? "BK-{$b->booking_id}",
                'clientUsername' => $b->client_username,
                'clientFullName' => $b->client_name ?? $b->client_username,
                'workerUsername' => $b->worker_username,
                'workerFullName' => $b->worker_name ?? $b->worker_username,
                'serviceCategory' => $b->service_category ?? 'General Repair',
                'taskDescription' => $b->task_description ?? '',
                'serviceAddress' => $b->service_address ?? '',
                'barangay' => $b->barangay ?? 'San Nicolas 1st',
                'estimatedBudget' => $b->estimated_budget ?? '₱500.00',
                'scheduledDate' => $b->scheduled_date ?? now()->format('M d, Y'),
                'status' => strtoupper($b->status ?? 'PENDING'),
                'timestamp' => $b->created_at ? $b->created_at->timestamp * 1000 : now()->timestamp * 1000,
            ];
        });

        return response()->json($bookings, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clientUsername' => 'required|string',
            'workerUsername' => 'required|string',
            'serviceCategory' => 'required|string',
            'taskDescription' => 'required|string',
            'serviceAddress' => 'required|string',
            'barangay' => 'required|string',
            'estimatedBudget' => 'required|string',
            'scheduledDate' => 'required|string',
        ]);

        $client = User::where('name', $validated['clientUsername'])
            ->orWhere('user_id', $validated['clientUsername'])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($validated['clientUsername'])])
            ->first();
        $worker = User::where('name', $validated['workerUsername'])
            ->orWhere('user_id', $validated['workerUsername'])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($validated['workerUsername'])])
            ->first();

        $refNumber = $request->input('id') ?: ('BK-' . rand(100000, 999999));

        // 🌟 SCHEDULE-CONFLICT PROTECTION: Prevent booking if worker is already booked on this date
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
                ->where(function($q) use ($parsedDate) {
                    $q->whereDate('scheduled_date', $parsedDate)
                      ->orWhere('scheduled_date', 'like', "%{$parsedDate}%");
                })
                ->whereIn('status', ['PENDING', 'ACCEPTED', 'CONFIRMED', 'ASSIGNED', 'IN_PROGRESS', 'IN PROGRESS'])
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => "Hindi available ang skilled worker na si {$validated['workerUsername']} sa napiling petsa ({$validated['scheduledDate']}) dahil may existing schedule na ito sa araw na ito. Pumili ng ibang petsa."
                ], 422);
            }
        }

        // 🌟 BILATERAL MATCHING: Merge client's open job need in this category
        $matchedClientJob = JobPost::where(function ($q) use ($validated, $client) {
                if ($client) {
                    $q->where('client_id', $client->user_id)
                      ->orWhere('posted_by', $client->name);
                } else {
                    $q->where('posted_by', $validated['clientUsername']);
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

        if ($matchedClientJob) {
            $matchedClientJob->status = 'Matched';
            $matchedClientJob->applicant_username = $worker ? $worker->name : $validated['workerUsername'];
            $matchedClientJob->save();
        }

        // 🌟 BILATERAL MATCHING: Merge worker's open service offer in this category
        $matchedWorkerOffer = JobPost::where(function ($q) use ($validated, $worker) {
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
            'client_username' => $validated['clientUsername'],
            'worker_username' => $worker ? $worker->name : $validated['workerUsername'],
            'client_name' => $client ? $client->full_name : $validated['clientUsername'],
            'worker_name' => $worker ? $worker->full_name : $validated['workerUsername'],
            'service_category' => $validated['serviceCategory'],
            'task_description' => $validated['taskDescription'],
            'service_address' => $validated['serviceAddress'],
            'barangay' => $validated['barangay'],
            'estimated_budget' => $validated['estimatedBudget'],
            'scheduled_date' => $validated['scheduledDate'],
            'status' => 'PENDING',
        ]);

        // Dispatch Email Notification to Worker
        if ($worker && !empty($worker->email)) {
            try {
                Mail::to($worker->email)->send(new BookingAlertMail($booking));
            } catch (\Throwable $e) {
                Log::warning('Email notification to worker failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Direct booking request submitted successfully!',
            'booking' => $booking,
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $booking = Booking::where('booking_reference', $id)
            ->orWhere('booking_id', $id)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        $newStatus = strtoupper($validated['status']);

        // 🌟 SCHEDULE-CONFLICT PROTECTION: Prevent accepting if worker already has an active booking on this date
        if ($newStatus === 'ACCEPTED') {
            $parsedDate = null;
            if (!empty($booking->scheduled_date)) {
                $rawDate = trim(explode('(', $booking->scheduled_date)[0]);
                $ts = strtotime($rawDate);
                if ($ts !== false) {
                    $parsedDate = date('Y-m-d', $ts);
                }
            }

            $conflictQuery = Booking::where('booking_id', '!=', $booking->booking_id)
                ->where(function($q) use ($booking) {
                    $q->where('worker_username', $booking->worker_username)
                      ->orWhere('worker_id', $booking->worker_id);
                })
                ->whereIn('status', ['ACCEPTED', 'CONFIRMED', 'ASSIGNED', 'IN_PROGRESS', 'IN PROGRESS']);

            if ($parsedDate) {
                $conflictQuery->where(function($q) use ($parsedDate, $booking) {
                    $q->whereDate('scheduled_date', $parsedDate)
                      ->orWhere('scheduled_date', 'like', "%{$parsedDate}%");
                });
            } else {
                $conflictQuery->where('scheduled_date', $booking->scheduled_date);
            }

            $conflictBooking = $conflictQuery->first();

            if ($conflictBooking) {
                return response()->json([
                    'success' => false,
                    'message' => "Schedule Conflict: May tinanggap ka nang serbisyo sa araw na ito ({$booking->scheduled_date}) para sa booking #{$conflictBooking->booking_reference}. Hindi maaaring mag-overlap o tumanggap ng magkasabay na booking sa parehong petsa."
                ], 422);
            }
        }

        $booking->status = $newStatus;
        if ($booking->status === 'COMPLETED') {
            $booking->completion_date = now()->toDateString();
        }
        $booking->save();

        return response()->json([
            'message' => "Booking status updated to {$booking->status} successfully!",
            'booking' => $booking,
        ], 200);
    }
}
