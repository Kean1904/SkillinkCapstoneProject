<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function checkUsername(Request $request)
    {
        $username = trim($request->query('username', ''));
        if ($username === '') {
            return response()->json([
                'exists'    => false,
                'available' => false,
                'message'   => 'Pakilagay ang username'
            ]);
        }

        $exists = User::whereRaw('LOWER(name) = ?', [strtolower($username)])->exists();

        if ($exists) {
            return response()->json([
                'exists'    => true,
                'available' => false,
                'message'   => 'Username has already exist'
            ]);
        }

        return response()->json([
            'exists'    => false,
            'available' => true,
            'message'   => 'Username is available'
        ]);
    }

    public function store(Request $request)
    {
        // 1. Compute age from date of birth (dob) if provided
        $dobInput = $request->input('dob') ?? $request->input('date_of_birth');
        if (!empty($dobInput)) {
            try {
                $parsedDob = \Carbon\Carbon::parse($dobInput);
                $calculatedAge = $parsedDob->age;
                $dob = $parsedDob->format('Y-m-d');
                $request->merge(['age' => $calculatedAge, 'dob' => $dob]);
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['dob' => 'Pakilagay ang tamang format ng Date of Birth.']);
            }
        } else {
            $dob = null;
        }

        // Validate the data
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'suffix'      => 'nullable|string|max:50',
            'dob'         => 'required_without:age|nullable|date',
            'age'         => 'required|integer|min:18|max:100',
            'gender'      => 'required|string',
            'address'     => 'required|string|max:255',
            'barangay'    => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'cellphone'   => 'required|string|max:11',
            'role'        => 'required|string',
            'username'    => 'required|string|min:10|max:22|unique:users,name',
            'password'    => ['required', 'string', 'min:10', 'max:22', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'username.unique'      => 'Username has already exist',
            'username.max'         => 'Ang username ay may maximum na 22 characters lamang.',
            'username.min'         => 'Ang username ay dapat may 10 hanggang 22 characters.',
            'password.min'         => 'Ang password ay dapat may 10 hanggang 22 characters.',
            'password.max'         => 'Ang password ay may maximum na 22 characters lamang.',
            'password.regex'       => 'Ang password ay dapat mayroong kahit isang uppercase letter, number, at special character.',
            'age.min'              => 'Ang minimum na edad ay 18 pataas (bawal ang 17 pababa alinsunod sa batas laban sa child labor).',
            'dob.required_without' => 'Kinakailangang ilagay ang Date of Birth.',
        ]);

        $role = strtolower(trim($validated['role']));
        $username = trim($validated['username']);

        // Guard: Admin at PESO Staff roles cannot be registered through public registration
        if (!in_array($role, ['skilled worker', 'household client'])) {
            return back()->withInput()->withErrors([
                'role' => 'Ang pagpaparehistro ay para lamang sa Skilled Worker at HouseHold Client. Ang Admin at PESO Staff accounts ay pinamamahalaan ng pamunuan.'
            ]);
        }

        $isSkilledWorker = ($role === 'skilled worker');

        // 2. Save to database
        $user = User::create([
            'first_name'        => $validated['first_name'],
            'middle_name'       => !empty($validated['middle_name']) ? trim($validated['middle_name']) : null,
            'last_name'         => $validated['last_name'],
            'suffix'            => !empty($validated['suffix']) ? trim($validated['suffix']) : null,
            'name'              => $username,
            'email'             => $validated['email'],
            'password_hash'     => Hash::make($validated['password']),
            'role'              => $role,
            'age'               => $validated['age'],
            'date_of_birth'     => $dob,
            'gender'            => $validated['gender'],
            'barangay'          => $validated['barangay'],
            'contact_number'    => $validated['cellphone'],
            'address'           => $validated['address'],
            'location_tag'      => $validated['barangay'],
            'skills'            => $request->input('skills', $isSkilledWorker ? 'General Handyman' : null),
            'certificate_proof' => ($request->input('certificate_proof') === 'Other' && $request->filled('other_certificate_proof'))
                                    ? $request->input('other_certificate_proof')
                                    : (in_array($request->input('certificate_proof'), ['', 'Wala', null], true) ? null : $request->input('certificate_proof')),
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

        // 4. Record Audit Log event
        \App\Models\AuditLog::log(
            'USER_REGISTRATION',
            "New {$role} account registered ({$user->name}) in Brgy. {$user->barangay}" . ($user->skills ? " with skill: {$user->skills}" : ""),
            $user->name,
            $role,
            $user->user_id
        );

        // 5. Redirect after success
        return redirect()->route('Login')->with('success', 'Account created successfully! You may now log in.');
    }
}