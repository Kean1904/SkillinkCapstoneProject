<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        // I-save sa session ang info ng naka-login
        Session::put('user_id', $user->user_id);
        Session::put('user_name', $user->name);
        Session::put('full_name', $user->first_name . ' ' . $user->last_name);
        Session::put('user_role', $user->role);
        Session::put('profile_image_uri', $user->profile_image_uri);
        \Illuminate\Support\Facades\Auth::login($user);

        // I-redirect base sa role
        return match ($user->role) {
            'admin'          => redirect()->route('dashboard.Admin'),
            'peso staff'     => redirect()->route('dashboard.PesoStaff'),
            'skilled worker' => redirect()->route('dashboard.SkilledWorker'),
            'residential'    => redirect()->route('dashboard.Residential'),
            default          => redirect()->route('Login')->withErrors(['username' => 'Unknown role.']),
        };
    }

    public function logout(Request $request)
    {
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

        // 3. Dispatch real email gamit ang Mailable
        $emailDispatched = false;
        try {
            Mail::to($targetEmail)->send(new PasswordResetMail($user, $resetUrl, $otp));
            $emailDispatched = true;
            Log::info("Password reset email successfully sent to {$targetEmail}");
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset email to {$targetEmail}: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Naipadala na ang password reset link at OTP code sa {$targetEmail}. Pakitingnan ang iyong inbox o spam folder.",
            'targetEmail' => $targetEmail,
            'emailDispatched' => $emailDispatched,
            'resetUrl' => $resetUrl, // Provided for direct verification / dev convenience
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