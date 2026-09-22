<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the data
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'age'         => 'required|integer|min:15|max:100',
            'gender'      => 'required|string',
            'address'     => 'required|string|max:255',
            'barangay'    => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'cellphone'   => 'required|string|max:11',
            'role'        => 'required|string',
            'username'    => 'required|string|max:255|unique:users,name',
            'password'    => 'required|string|min:6',
        ]);

        $role = strtolower(trim($validated['role']));
        $username = trim($validated['username']);

        // 🌟 Suffix / Extension Name Validation para sa Admin at PESO Staff
        if ($role === 'admin') {
            if (!str_ends_with(strtolower($username), '@admin')) {
                return back()->withInput()->withErrors([
                    'username' => 'Ang Admin username ay kinakailangang magtapos sa extension name na @admin o @Admin (halimbawa: ' . (str_contains($username, '@') ? explode('@', $username)[0] : $username) . '@Admin).'
                ]);
            }
        } elseif ($role === 'peso staff' || $role === 'staff') {
            if (!str_ends_with(strtolower($username), '@staff')) {
                return back()->withInput()->withErrors([
                    'username' => 'Ang PESO Staff username ay kinakailangang magtapos sa extension name na @staff o @Staff (halimbawa: ' . (str_contains($username, '@') ? explode('@', $username)[0] : $username) . '@Staff).'
                ]);
            }
        } else {
            // Residential at Skilled Worker ay bawal gumamit ng @admin o @staff
            if (str_ends_with(strtolower($username), '@admin') || str_ends_with(strtolower($username), '@staff')) {
                return back()->withInput()->withErrors([
                    'username' => 'Ang extension na @admin at @staff ay nakalaan lamang para sa mga opisyal ng PESO at Administrator.'
                ]);
            }
        }

        $isSkilledWorker = ($role === 'skilled worker');

        // 2. Save to database
        $user = User::create([
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'name'              => $validated['username'],
            'email'             => $validated['email'],
            'password_hash'     => Hash::make($validated['password']),
            'role'              => $role,
            'age'               => $validated['age'],
            'gender'            => $validated['gender'],
            'barangay'          => $validated['barangay'],
            'contact_number'    => $validated['cellphone'],
            'address'           => $validated['address'],
            'location_tag'      => $validated['barangay'],
            'skills'            => $request->input('skills', $isSkilledWorker ? 'General Handyman' : null),
            'certificate_proof' => ($request->input('certificate_proof') === 'Other' && $request->filled('other_certificate_proof'))
                                    ? $request->input('other_certificate_proof')
                                    : $request->input('certificate_proof', null),
            'is_verified'       => $isSkilledWorker ? false : true, // Skilled workers await PESO accreditation
            'rating'                      => 5.00,
            'status'                      => 'active',
            'privacy_consent_accepted'    => (bool) $request->input('privacy_consent_accepted', true),
            'privacy_consent_accepted_at' => now(),
        ]);

        // 3. Create worker_profile if skilled worker
        if ($isSkilledWorker) {
            DB::table('worker_profiles')->insert([
                'user_id'             => $user->user_id,
                'skill_tags'          => $request->input('skills', 'General Handyman'),
                'service_categories'  => $request->input('skills', 'General Handyman'),
                'biography'           => 'Registered skilled worker in Brgy. ' . $validated['barangay'] . ', Magalang.',
                'average_rating'      => 5.00,
                'availability_status' => 'available',
                'experience_years'    => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // 4. Redirect after success
        return redirect()->route('Login')->with('success', 'Account created successfully! You may now log in.');
    }
}