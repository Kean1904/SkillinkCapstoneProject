<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
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

        $client = User::where('name', $validated['clientUsername'])->first();
        $worker = User::where('name', $validated['workerUsername'])->first();
        $workerProfile = $worker ? DB::table('worker_profiles')->where('user_id', $worker->user_id)->first() : null;

        $refNumber = 'BK-' . rand(100000, 999999);

        $booking = Booking::create([
            'booking_reference' => $refNumber,
            'request_id' => 1,
            'worker_id' => $workerProfile ? $workerProfile->worker_id : 1,
            'client_username' => $validated['clientUsername'],
            'worker_username' => $validated['workerUsername'],
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

        $booking->status = strtoupper($validated['status']);
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
