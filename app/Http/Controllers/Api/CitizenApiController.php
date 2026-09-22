<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class CitizenApiController extends Controller
{
    public function getWorkers(Request $request)
    {
        $query = User::where('role', 'skilled worker');

        if ($request->has('barangay') && $request->barangay) {
            $query->where('barangay', $request->barangay);
        }

        $workers = $query->get()->map(function ($u) {
            return [
                'username' => $u->name,
                'firstName' => $u->first_name ?? '',
                'lastName' => $u->last_name ?? '',
                'role' => 'SKILLED WORKER',
                'barangay' => $u->barangay ?? 'San Nicolas 1st',
                'address' => $u->address ?? '',
                'email' => $u->email,
                'phoneNumber' => $u->contact_number ?? '',
                'cellphone' => $u->contact_number ?? '',
                'skills' => $u->skills ?? 'General Handyman',
                'certificateProof' => $u->certificate_proof,
                'isVerified' => (bool) $u->is_verified,
                'rating' => (float) ($u->rating ?? 5.0),
                'profileImageUri' => $u->profile_image_uri,
            ];
        });

        return response()->json($workers, 200);
    }

    public function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role && strtolower($request->role) !== 'all') {
            $role = strtolower($request->role);
            if (str_contains($role, 'skilled')) {
                $query->where('role', 'skilled worker');
            } elseif (str_contains($role, 'resident')) {
                $query->where('role', 'residential');
            } elseif (str_contains($role, 'staff')) {
                $query->where('role', 'peso staff');
            } elseif (str_contains($role, 'admin')) {
                $query->where('role', 'admin');
            }
        }

        if ($request->has('search') && $request->search) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%")
                  ->orWhere('skills', 'like', "%{$search}%");
            });
        }

        $users = $query->get()->map(function ($u) {
            return [
                'username' => $u->name,
                'firstName' => $u->first_name ?? '',
                'lastName' => $u->last_name ?? '',
                'role' => $u->role_display,
                'barangay' => $u->barangay ?? 'San Nicolas 1st',
                'address' => $u->address ?? '',
                'email' => $u->email,
                'phoneNumber' => $u->contact_number ?? '',
                'cellphone' => $u->contact_number ?? '',
                'skills' => $u->skills ?? '',
                'certificateProof' => $u->certificate_proof,
                'isVerified' => (bool) $u->is_verified,
                'rating' => (float) ($u->rating ?? 5.0),
                'profileImageUri' => $u->profile_image_uri,
                'createdAt' => $u->created_at_display,
                'isOnline' => (bool) $u->is_online,
                'status' => $u->status_label,
                'lastActivityAt' => $u->last_seen_display,
            ];
        });

        return response()->json($users, 200);
    }

    public function postReview(Request $request)
    {
        $clientUsername = $request->input('clientUsername');
        $workerUsername = $request->input('workerUsername');
        $ratingStars = (int) ($request->input('ratingStars') ?? $request->input('rating') ?? 5);
        $reviewText = $request->input('reviewText') ?? $request->input('comment') ?? '';

        if (empty($clientUsername) || empty($workerUsername)) {
            return response()->json(['message' => 'clientUsername and workerUsername are required.'], 422);
        }

        $client = User::where('name', $clientUsername)->first();
        $worker = User::where('name', $workerUsername)->first();
        $workerProfile = $worker ? DB::table('worker_profiles')->where('user_id', $worker->user_id)->first() : null;

        $bookingId = null;
        if ($request->has('bookingId') && !empty($request->input('bookingId'))) {
            $booking = Booking::where('booking_reference', $request->input('bookingId'))
                ->orWhere('booking_id', $request->input('bookingId'))
                ->first();
            $bookingId = $booking ? $booking->booking_id : null;
        }

        $review = Review::create([
            'booking_id' => $bookingId,
            'client_id' => $client ? $client->user_id : 1,
            'worker_id' => $workerProfile ? $workerProfile->worker_id : 1,
            'client_username' => $clientUsername,
            'worker_username' => $workerUsername,
            'rating_score' => $ratingStars,
            'review_text' => $reviewText,
            'created_at' => now(),
        ]);

        // Recompute worker rating
        if ($worker) {
            $avg = Review::where('worker_username', $worker->name)->avg('rating_score') ?: $ratingStars;
            $worker->rating = round($avg, 2);
            $worker->save();
        }

        return response()->json([
            'message' => 'Review submitted successfully! Thank you for rating.',
            'review' => $review,
        ], 201);
    }

    public function postComplaint(Request $request)
    {
        $complainantUsername = $request->input('complainantUsername');
        $respondentUsername = $request->input('respondentUsername');
        $complaintType = $request->input('complaintType') ?? $request->input('serviceCategory') ?? 'Service Dispute';
        $description = $request->input('description') ?? $request->input('details') ?? '';

        if (empty($complainantUsername) || empty($respondentUsername) || empty($description)) {
            return response()->json(['message' => 'complainantUsername, respondentUsername, and description are required.'], 422);
        }

        $complainant = User::where('name', $complainantUsername)->first();

        $bookingId = null;
        if ($request->has('bookingId') && !empty($request->input('bookingId'))) {
            $booking = Booking::where('booking_reference', $request->input('bookingId'))
                ->orWhere('booking_id', $request->input('bookingId'))
                ->first();
            $bookingId = $booking ? $booking->booking_id : null;
        }

        $complaint = Complaint::create([
            'booking_id' => $bookingId,
            'submitted_by' => $complainant ? $complainant->user_id : 1,
            'complainant_username' => $complainantUsername,
            'respondent_username' => $respondentUsername,
            'complaint_type' => $complaintType,
            'description' => $description,
            'status' => 'Pending Investigation',
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Official complaint filed successfully! PESO Staff will investigate.',
            'complaint' => $complaint,
        ], 201);
    }

    public function heartbeat(Request $request)
    {
        $username = $request->input('username') ?? $request->input('name');
        if (!$username && $request->user()) {
            $username = $request->user()->name;
        }

        if ($username) {
            $user = User::where('name', $username)->first();
            if ($user) {
                $user->update(['last_seen_at' => now()]);
                return response()->json([
                    'success' => true,
                    'message' => 'Heartbeat acknowledged',
                    'username' => $username,
                    'last_seen_at' => $user->last_seen_at->toIso8601String(),
                    'isOnline' => true,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found or username not provided',
        ], 404);
    }
}
