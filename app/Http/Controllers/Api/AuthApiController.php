<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
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
                return response()->json([
                    'message' => 'Ang Admin username ay kinakailangang magtapos sa extension name na @admin o @Admin (hal. username@Admin).'
                ], 422);
            }
        } elseif ($role === 'peso staff' || $role === 'staff') {
            if (!str_ends_with(strtolower($username), '@staff')) {
                return response()->json([
                    'message' => 'Ang PESO Staff username ay kinakailangang magtapos sa extension name na @staff o @Staff (hal. username@Staff).'
                ], 422);
            }
        } else {
            if (str_ends_with(strtolower($username), '@admin') || str_ends_with(strtolower($username), '@staff')) {
                return response()->json([
                    'message' => 'Ang extension na @admin at @staff ay nakalaan lamang para sa mga opisyal ng PESO at Administrator.'
                ], 422);
            }
        }

        $isSkilled = ($role === 'skilled worker');

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
            'skills'            => $request->input('skills', $isSkilled ? 'General Handyman' : null),
            'certificate_proof' => $request->input('certificateProof', $request->input('certificate_proof', null)),
            'is_verified'       => $isSkilled ? false : true,
            'rating'            => 5.00,
            'status'            => 'active',
        ]);

        if ($isSkilled) {
            DB::table('worker_profiles')->insert([
                'user_id'             => $user->user_id,
                'skill_tags'          => $request->input('skills', 'General Handyman'),
                'service_categories'  => $request->input('skills', 'General Handyman'),
                'biography'           => 'Registered skilled worker in Magalang.',
                'average_rating'      => 5.00,
                'availability_status' => 'available',
                'experience_years'    => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        $token = $user->createToken('android-app')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully!',
            'token'   => $token,
            'user'    => [
                'user_id'          => $user->user_id,
                'id'               => $user->user_id,
                'fullName'         => $user->full_name,
                'full_name'        => $user->full_name,
                'firstName'        => $user->first_name,
                'lastName'         => $user->last_name,
                'username'         => $user->name,
                'email'            => $user->email,
                'role'             => $user->role,
                'barangay'         => $user->barangay,
                'cellphone'        => $user->contact_number,
                'phoneNumber'      => $user->contact_number,
                'skills'           => $user->skills,
                'certificateProof' => $user->certificate_proof,
                'isVerified'       => (bool) $user->is_verified,
                'rating'           => (float) ($user->rating ?? 5.0),
                'profileImageUri'  => $user->profile_image_uri,
            ],
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'message' => 'Invalid username or password.',
            ], 401);
        }

        $token = $user->createToken('android-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token'   => $token,
            'user'    => [
                'user_id'          => $user->user_id,
                'id'               => $user->user_id,
                'fullName'         => $user->full_name,
                'full_name'        => $user->full_name,
                'firstName'        => $user->first_name,
                'lastName'         => $user->last_name,
                'username'         => $user->name,
                'email'            => $user->email,
                'role'             => $user->role,
                'barangay'         => $user->barangay,
                'cellphone'        => $user->contact_number,
                'phoneNumber'      => $user->contact_number,
                'skills'           => $user->skills,
                'certificateProof' => $user->certificate_proof,
                'isVerified'       => (bool) $user->is_verified,
                'rating'           => (float) ($user->rating ?? 5.0),
                'profileImageUri'  => $user->profile_image_uri,
            ],
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
