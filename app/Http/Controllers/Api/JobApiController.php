<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPost;
use App\Models\User;
use App\Mail\ApplicationAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class JobApiController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::query()->latest('created_at');

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('barangay') && $request->barangay) {
            $query->where('barangay', $request->barangay);
        }

        if ($request->has('status') && $request->status) {
            $status = strtolower($request->status);
            if ($status === 'available') {
                $query->where(function ($q) {
                    $q->whereNull('applicant_username')
                      ->orWhere('applicant_username', '');
                })->whereNotIn('status', ['Completed', 'Cancelled']);
            } elseif ($status === 'applied') {
                $query->whereNotNull('applicant_username')->where('status', 'Applied');
            } else {
                $query->where('status', $request->status);
            }
        }

        $jobs = $query->get()->map(function ($job) {
            $isAvailable = empty($job->applicant_username) && !in_array($job->status, ['Completed', 'Cancelled']);
            return [
                'id'                => (string) $job->request_id,
                'title'             => $job->title ?? 'Service Requirement',
                'description'       => $job->description ?? '',
                'category'          => $job->category ?? 'General Repair',
                'barangay'          => $job->barangay ?? 'San Nicolas 1st',
                'postedBy'          => $job->posted_by ?? 'Resident',
                'status'            => $job->status ?? 'Pending',
                'applicantUsername' => $job->applicant_username,
                'isAvailable'       => $isAvailable,
                'isTaken'           => !empty($job->applicant_username),
                'canApply'          => $isAvailable,
                'datePosted'        => $job->date_posted,
                'timestamp'         => $job->created_at ? $job->created_at->timestamp * 1000 : now()->timestamp * 1000,
            ];
        });

        return response()->json($jobs, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'barangay' => 'required|string|max:100',
            'postedBy' => 'required|string|max:100',
        ]);

        $client = User::where('name', $validated['postedBy'])->first();

        $job = JobPost::create([
            'title' => $validated['title'],
            'client_id' => $client ? $client->user_id : 1,
            'posted_by' => $validated['postedBy'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'location_tag' => $validated['barangay'],
            'barangay' => $validated['barangay'],
            'preferred_schedule' => $request->input('preferredSchedule', 'Flexible'),
            'date_posted' => now()->toDateString(),
            'status' => 'Pending',
            'applicant_username' => null,
        ]);

        return response()->json([
            'message' => 'Job requirement posted successfully!',
            'job' => $job,
        ], 201);
    }

    public function apply(Request $request, $id)
    {
        $validated = $request->validate([
            'applicantUsername' => 'required|string',
        ]);

        $job = JobPost::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found.'], 404);
        }

        $job->applicant_username = $validated['applicantUsername'];
        $job->status = 'Applied';
        $job->save();

        $applicant = User::where('name', $validated['applicantUsername'])->first() ?? new User(['name' => $validated['applicantUsername']]);

        // Notify Job Poster / PESO Staff via Email
        $poster = User::where('name', $job->posted_by)->orWhere('user_id', $job->client_id)->first();
        if ($poster && !empty($poster->email)) {
            try {
                Mail::to($poster->email)->send(new ApplicationAlertMail($job, $applicant));
            } catch (\Throwable $e) {
                Log::warning('Email alert for job application failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Application submitted successfully! PESO and Client have been notified.',
            'job' => $job,
        ], 200);
    }
}
