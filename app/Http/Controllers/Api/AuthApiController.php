<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        // 1. Compute age from date of birth (dob) if provided
        $dobInput = $request->input('dob') ?? $request->input('date_of_birth') ?? $request->input('dateOfBirth');
        if (!empty($dobInput)) {
            try {
                $parsedDob = \Carbon\Carbon::parse($dobInput);
                $calculatedAge = $parsedDob->age;
                $dob = $parsedDob->format('Y-m-d');
                $request->merge(['age' => $calculatedAge, 'dob' => $dob]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Pakilagay ang tamang format ng Date of Birth.'
                ], 422);
            }
        } else {
            $dob = null;
        }

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

        $isSkilled = ($role === 'skilled worker');

        $middleName = $request->input('middle_name') ?? $request->input('middleName');
        $suffix = $request->input('suffix');

        $user = User::create([
            'first_name'        => $validated['first_name'],
            'middle_name'       => !empty($middleName) ? trim($middleName) : null,
            'last_name'         => $validated['last_name'],
            'suffix'            => !empty($suffix) ? trim($suffix) : null,
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
            'skills'            => $request->input('skills', $isSkilled ? 'General Handyman' : null),
            'certificate_proof' => (($request->input('certificateProof') === 'Other' || $request->input('certificate_proof') === 'Other') && ($request->filled('otherCertificateProof') || $request->filled('other_certificate_proof')))
                                    ? ($request->input('otherCertificateProof') ?? $request->input('other_certificate_proof'))
                                    : $request->input('certificateProof', $request->input('certificate_proof', null)),
            'is_verified'       => $isSkilled ? false : true,
            'rating'            => 5.00,
            'status'            => 'active',
            'privacy_consent_accepted'    => true,
            'privacy_consent_accepted_at' => now(),
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
                'user_id'                 => $user->user_id,
                'id'                      => $user->user_id,
                'fullName'                => $user->full_name,
                'full_name'               => $user->full_name,
                'firstName'               => $user->first_name,
                'lastName'                => $user->last_name,
                'username'                => $user->name,
                'email'                   => $user->email,
                'role'                    => $user->role,
                'barangay'                => $user->barangay,
                'cellphone'               => $user->contact_number,
                'phoneNumber'             => $user->contact_number,
                'skills'                  => $user->skills,
                'certificateProof'        => $user->certificate_proof,
                'isVerified'              => (bool) $user->is_verified,
                'rating'                  => (float) ($user->rating ?? 5.0),
                'profileImageUri'         => $user->profile_image_uri,
                'isConsentAccepted'       => true,
                'privacyConsentAccepted'  => true,
                'privacy_consent_accepted'=> true,
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

        // Account Deactivation & 30-day Grace Period check
        if ($user->deletion_scheduled_at) {
            if (now()->gte($user->deletion_scheduled_at)) {
                return response()->json([
                    'message' => 'Ang account na ito ay tuluyan nang nabura matapos ang 30-araw na palugit alinsunod sa Data Privacy Act of 2012.',
                ], 403);
            } else {
                // Reactivate account during 30-day grace period
                $user->status = 'active';
                $user->deactivated_at = null;
                $user->deletion_scheduled_at = null;
                $user->deactivation_reason = null;
                $user->save();

                \App\Models\AuditLog::log(
                    'ACCOUNT_REACTIVATED',
                    "User {$user->name} reactivated their account during the 30-day grace period via Mobile.",
                    $user->name,
                    $user->role,
                    $user->user_id
                );
            }
        }

        $user->update(['last_seen_at' => now()]);

        $token = $user->createToken('android-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token'   => $token,
            'user'    => [
                'user_id'                 => $user->user_id,
                'id'                      => $user->user_id,
                'fullName'                => $user->full_name,
                'full_name'               => $user->full_name,
                'firstName'               => $user->first_name,
                'lastName'                => $user->last_name,
                'username'                => $user->name,
                'email'                   => $user->email,
                'role'                    => $user->role,
                'barangay'                => $user->barangay,
                'cellphone'               => $user->contact_number,
                'phoneNumber'             => $user->contact_number,
                'skills'                  => $user->skills,
                'certificateProof'        => $user->certificate_proof,
                'isVerified'              => (bool) $user->is_verified,
                'rating'                  => (float) ($user->rating ?? 5.0),
                'profileImageUri'         => $user->profile_image_uri,
                'isConsentAccepted'       => (bool) $user->privacy_consent_accepted,
                'privacyConsentAccepted'  => (bool) $user->privacy_consent_accepted,
                'privacy_consent_accepted'=> (bool) $user->privacy_consent_accepted,
            ],
        ], 200);
    }

    /**
     * Mobile API: Update Data Privacy Consent for User
     */
    public function updateConsent(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            $identifier = $request->input('user_id') ?? $request->input('username') ?? $request->input('email');
            if ($identifier) {
                $user = User::where('user_id', $identifier)
                    ->orWhere('name', $identifier)
                    ->orWhere('email', $identifier)
                    ->first();
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $newConsent = $request->has('consent') ? filter_var($request->input('consent'), FILTER_VALIDATE_BOOLEAN) : true;
        $user->privacy_consent_accepted = $newConsent;
        $user->privacy_consent_accepted_at = $newConsent ? now() : null;
        $user->save();

        return response()->json([
            'success'                => true,
            'message'                => $newConsent
                ? 'Matagumpay na naitala ang iyong pahintulot sa Data Privacy.'
                : 'Pansamantalang binawi ang Data Privacy Consent.',
            'isConsentAccepted'      => $newConsent,
            'privacyConsentAccepted' => $newConsent,
            'privacy_consent_accepted' => $newConsent,
        ]);
    }

    /**
     * Mobile API: Deactivate / Delete Account with 30-Day Grace Period
     */
    public function deactivateAccount(Request $request)
    {
        $identifier = $request->input('username') ?? $request->input('user_id') ?? $request->input('email');
        $user = null;
        if ($request->user()) {
            $user = $request->user();
        } elseif ($identifier) {
            $user = User::where('name', $identifier)
                ->orWhere('user_id', $identifier)
                ->orWhere('email', $identifier)
                ->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User account not found.',
            ], 404);
        }

        $reason = $request->input('reason', "This is temporary. I'll be back.");
        if ($reason === 'Other' && $request->filled('other_reason')) {
            $reason = 'Other: ' . trim($request->input('other_reason'));
        }

        $user->status = 'DEACTIVATED';
        $user->deactivated_at = now();
        $user->deletion_scheduled_at = now()->addDays(30);
        $user->deactivation_reason = $reason;
        $user->save();

        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        \App\Models\AuditLog::log(
            'ACCOUNT_DEACTIVATED',
            "User {$user->name} ({$user->role}) requested account deactivation with 30-day scheduled deletion via Mobile. Reason: {$reason}",
            $user->name,
            $user->role,
            $user->user_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Nai-deactivate na ang iyong account. Mayroon kang 30 araw na palugit bago tuluyang mabura ang iyong profile at datos sa system.',
            'deletion_scheduled_at' => $user->deletion_scheduled_at ? $user->deletion_scheduled_at->toIso8601String() : null,
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

    /**
     * Mobile API: Request Password Reset via Email
     */
    public function forgotPassword(Request $request)
    {
        @set_time_limit(60);

        $input = $request->input('email_or_username') ?? $request->input('email') ?? $request->input('username');

        if (empty($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide your registered email address or username.',
            ], 422);
        }

        $query = trim($input);
        $user = User::where('email', $query)->orWhere('name', $query)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No SKILLINK account found with that email or username.',
            ], 404);
        }

        $targetEmail = $user->email;
        if (empty($targetEmail)) {
            return response()->json([
                'success' => false,
                'message' => 'No email address registered for this account. Please contact PESO Magalang.',
            ], 422);
        }

        $token = Str::random(60);
        $otp = strval(rand(100000, 999999));

        DB::table('password_resets')->updateOrInsert(
            ['email' => $targetEmail],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset.form', [
            'token' => $token,
            'email' => $targetEmail,
        ]);

        $recipientName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;
        $mailResult = \App\Services\BrevoOtpService::sendPasswordResetOtp($targetEmail, $recipientName, $otp, $resetUrl);

        return response()->json([
            'success' => true,
            'email_sent' => $mailResult['success'],
            'message' => $mailResult['success']
                ? "Password reset OTP sent to registered email ({$targetEmail})."
                : "Password reset OTP generated for {$targetEmail}.",
            'targetEmail' => $targetEmail,
            'otp' => $otp,
            'token' => $token,
            'resetUrl' => $resetUrl,
        ], 200);
    }

    /**
     * Mobile API: Submit New Password with Token or OTP
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired security token.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->password_hash = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You can now log in with your new credentials.',
        ], 200);
    }

    // CHECK USERNAME UNIQUENESS
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
}
