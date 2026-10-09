<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors(['username' => 'Invalid username or password.']);
        }

        // Account Deactivation & 30-day Grace Period check
        if ($user->deletion_scheduled_at) {
            if (now()->gte($user->deletion_scheduled_at)) {
                return back()->withErrors(['username' => 'Ang account na ito ay tuluyan nang nabura matapos ang 30-araw na palugit alinsunod sa Data Privacy Act of 2012.']);
            } else {
                // Reactivate account during 30-day grace period
                $user->status = 'active';
                $user->deactivated_at = null;
                $user->deletion_scheduled_at = null;
                $user->deactivation_reason = null;
                $user->save();

                \App\Models\AuditLog::log(
                    'ACCOUNT_REACTIVATED',
                    "User {$user->name} reactivated their account during the 30-day grace period.",
                    $user->name,
                    $user->role,
                    $user->user_id
                );

                Session::flash('success', 'Maligayang pagbabalik! Matagumpay na na-reactivate ang iyong account at nakansela ang nakatakdang pagbura.');
            }
        }

        // I-save sa session ang info ng naka-login
        Session::put('user_id', $user->user_id);
        Session::put('user_name', $user->name);
        Session::put('full_name', $user->full_name);
        Session::put('user_role', $user->role);
        Session::put('profile_image_uri', $user->profile_image_uri);
        Session::put('privacy_consent_accepted', (bool) $user->privacy_consent_accepted);
        \Illuminate\Support\Facades\Auth::login($user);

        // Record Audit Log event
        \App\Models\AuditLog::log(
            'USER_LOGIN',
            "User {$user->name} ({$user->role}) authenticated successfully.",
            $user->name,
            $user->role,
            $user->user_id
        );

        // I-redirect base sa role
        $roleLower = strtolower(trim($user->role));
        if ($roleLower === 'admin') {
            return redirect()->route('dashboard.Admin');
        } elseif ($roleLower === 'peso staff' || $roleLower === 'staff') {
            return redirect()->route('dashboard.PesoStaff');
        } elseif ($roleLower === 'skilled worker' || $roleLower === 'skilled') {
            return redirect()->route('dashboard.SkilledWorker');
        } elseif ($roleLower === 'residential' || str_contains($roleLower, 'household') || str_contains($roleLower, 'client')) {
            return redirect()->route('dashboard.HouseholdClient');
        } else {
            return redirect()->route('Login')->withErrors(['username' => 'Unknown role.']);
        }
    }

    /**
     * Accept Data Privacy Consent from the dashboard barrier modal.
     */
    public function acceptConsent(Request $request)
    {
        $userId = Session::get('user_id') ?? (\Illuminate\Support\Facades\Auth::id());
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Mangyaring mag-login muli.'], 401);
        }

        $user = User::where('user_id', $userId)->first();
        if ($user) {
            $user->privacy_consent_accepted = true;
            $user->privacy_consent_accepted_at = now();
            $user->last_seen_at = now();
            $user->save();

            Session::put('privacy_consent_accepted', true);

            return response()->json([
                'success' => true,
                'message' => 'Matagumpay na naitala ang iyong pahintulot sa Data Privacy.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    }

    /**
     * Toggle Data Privacy Consent from Settings.
     */
    public function toggleConsent(Request $request)
    {
        $userId = Session::get('user_id') ?? (\Illuminate\Support\Facades\Auth::id());
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Mangyaring mag-login muli.'], 401);
        }

        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $newConsent = $request->has('consent') ? filter_var($request->input('consent'), FILTER_VALIDATE_BOOLEAN) : !$user->privacy_consent_accepted;
        $user->privacy_consent_accepted = $newConsent;
        $user->privacy_consent_accepted_at = $newConsent ? now() : null;
        $user->save();

        Session::put('privacy_consent_accepted', $newConsent);

        return response()->json([
            'success' => true,
            'consent' => $newConsent,
            'message' => $newConsent
                ? 'Aktibo ang iyong Data Privacy Consent alinsunod sa RA 10173.'
                : 'Pansamantalang binawi ang Data Privacy Consent.',
        ]);
    }

    /**
     * Delete or Deactivate Account with 30-Day Grace Period.
     */
    public function deactivateAccount(Request $request)
    {
        $userId = Session::get('user_id') ?? (\Illuminate\Support\Facades\Auth::id());
        if (!$userId) {
            return redirect()->route('Login')->withErrors(['username' => 'Mangyaring mag-login muli bago i-deactivate ang account.']);
        }

        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('Login')->withErrors(['username' => 'User account not found.']);
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

        \App\Models\AuditLog::log(
            'ACCOUNT_DEACTIVATED',
            "User {$user->name} ({$user->role}) deactivated account with 30-day scheduled deletion. Reason: {$reason}",
            $user->name,
            $user->role,
            $user->user_id
        );

        \Illuminate\Support\Facades\Auth::logout();
        Session::flush();
        $request->session()->regenerate();

        return redirect()->route('Login')->with('success', 'Nai-deactivate na ang iyong account. Mayroon kang 30 araw na palugit bago tuluyang mabura ang iyong profile at datos. Kung nais mong bawiin, mag-log in lamang muli bago lumipas ang 30 araw.');
    }

    public function logout(Request $request)
    {
        $userId   = Session::get('user_id') ?? (\Illuminate\Support\Facades\Auth::id());
        $userName = Session::get('user_name', 'User');
        $userRole = Session::get('user_role', 'User');

        \App\Models\AuditLog::log(
            'USER_LOGOUT',
            "User {$userName} logged out of session.",
            $userName,
            $userRole,
            $userId
        );

        // Burahin lahat ng laman ng session
        Session::flush();

        // I-regenerate ang session ID (security best practice)
        $request->session()->regenerate();

        // I-redirect papunta sa Main Dashboard
        if ($request->query('redirect') === 'dashboard' || $request->query('reason') === 'idle') {
            return redirect()->route('home')->with('info', 'Session ended due to inactivity. You have been safely logged out to prevent unauthorized access and data leakage.');
        }

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Handle Forgot Password Request: Generate token, store in DB, and dispatch real email.
     */
    public function forgotPassword(Request $request)
    {
        @set_time_limit(60);

        $request->validate([
            'email_or_username' => 'required|string',
        ]);

        $query = trim($request->input('email_or_username'));
        $user = User::where('email', $query)->orWhere('name', $query)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Walang nahanap na SKILLINK account na may ganyang username o email.',
            ], 404);
        }

        $targetEmail = $user->email;
        if (empty($targetEmail)) {
            return response()->json([
                'success' => false,
                'message' => 'Walang naka-link na email address sa account na ito. Mangyaring makipag-ugnayan sa PESO Magalang.',
            ], 422);
        }

        // 1. Generate secure token at 6-digit OTP
        $token = Str::random(60);
        $otp = strval(rand(100000, 999999));

        // 2. I-save o i-update sa password_resets table
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

        // 3. Ipadala ang totoong OTP Authentication Email gamit si Brevo sa rehistradong email mula sa Database
        $recipientName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;
        $mailResult = \App\Services\BrevoOtpService::sendPasswordResetOtp($targetEmail, $recipientName, $otp, $resetUrl);

        // 4. Ibalik ang response para sa modal
        return response()->json([
            'success' => true,
            'email_sent' => $mailResult['success'],
            'mail_error' => $mailResult['error'] ?? null,
            'message' => $mailResult['success']
                ? "Matagumpay na naipadala ang 6-digit OTP code sa iyong rehistradong email ({$targetEmail})."
                : "Hindi ma-dispatch ni Brevo ang email sa ngayon: " . ($mailResult['error'] ?? 'SMTP Error'),
            'targetEmail' => $targetEmail,
            'otp' => $otp,
            'resetUrl' => $resetUrl,
        ]);
    }

    /**
     * Show the Password Reset Form when user clicks the email link.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');

        $record = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('Login')->with('error', 'Invalid o paso na ang password reset link. Mangyaring humiling muli.');
        }

        // Optional expiration check (hal. 30 minuto)
        if (now()->diffInMinutes($record->created_at) > 30) {
            DB::table('password_resets')->where('email', $email)->delete();
            return redirect()->route('Login')->with('error', 'Nag-expire na ang password reset link (lampas 30 minuto). Mangyaring humiling muli.');
        }

        return view('auth.reset_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Process Password Reset Submission and update user credentials.
     */
    public function updatePasswordWithToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['password' => 'Invalid o paso na ang security token para sa reset na ito.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['password' => 'Hindi natagpuan ang account para sa email na ito.']);
        }

        // I-update ang password_hash
        $user->password_hash = Hash::make($request->password);
        $user->save();

        // Burahin ang nagamit nang token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('Login')->with('success', 'Matagumpay nang nabago ang iyong password! Maaari ka nang mag-log in gamit ang bagong credentials.');
    }
}