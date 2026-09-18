<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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

        // I-redirect papunta sa Login page
        return redirect()->route('Login')->with('success', 'You have been logged out successfully.');
    }
}