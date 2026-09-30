<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. I-check muna kung naka-login (may session o Auth)
        $userId = session('user_id') ?? Auth::id();
        if (!$userId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please log in to continue.'], 401);
            }
            return redirect()->route('Login')->with('error', 'Mangyaring mag-login muna upang makapasok sa system.');
        }

        // 2. Kunin ang role ng naka-login mula sa session o database
        $userRole = session('user_role');
        if (!$userRole) {
            $user = Auth::user() ?? User::where('user_id', $userId)->first();
            if ($user) {
                $userRole = $user->role;
                session(['user_role' => $user->role]);
            }
        }

        $normalizedUserRole = $this->normalizeRole($userRole);

        // 3. I-normalize ang mga pinapayagang roles sa route
        $normalizedAllowedRoles = [];
        foreach ($roles as $role) {
            $parts = explode(',', $role);
            foreach ($parts as $part) {
                $normalizedAllowedRoles[] = $this->normalizeRole(trim($part));
            }
        }

        // 4. I-check kung tugma ang role ng user sa alinman sa mga pinapayagan
        if (!in_array($normalizedUserRole, $normalizedAllowedRoles, true)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Hindi pinapayagan ang iyong role sa operasyong ito.'
                ], 403);
            }

            // Hindi pinapayagan - ibalik sa sarili niyang dashboard
            return $this->redirectToOwnDashboard($normalizedUserRole);
        }

        // 5. Ituloy ang request
        $response = $next($request);

        // 6. Idagdag ang no-cache headers para sa browser back-button security
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }

    /**
     * I-normalize ang role string sa standard values:
     * - 'admin'
     * - 'peso staff'
     * - 'skilled worker'
     * - 'residential'
     */
    private function normalizeRole(?string $role): string
    {
        if (!$role) {
            return '';
        }

        $r = strtolower(trim($role));
        $r = str_replace(['_', '-'], ' ', $r);

        if ($r === 'admin' || str_contains($r, 'admin')) {
            return 'admin';
        }
        if ($r === 'peso staff' || $r === 'peso' || $r === 'pesostaff' || str_contains($r, 'peso') || $r === 'staff') {
            return 'peso staff';
        }
        if ($r === 'skilled worker' || $r === 'skilled' || $r === 'worker' || $r === 'skilledworker' || str_contains($r, 'skilled')) {
            return 'skilled worker';
        }
        if ($r === 'residential' || str_contains($r, 'household') || str_contains($r, 'client') || $r === 'resident') {
            return 'residential';
        }

        return $r;
    }

    /**
     * I-redirect ang user sa sarili niyang dashboard kapag sinubukan niyang pumunta sa ibang role
     */
    private function redirectToOwnDashboard(string $role): Response
    {
        $message = 'Bawal ma-access ang pahinang iyon dahil hindi ito angkop sa iyong account role. Ibinabalik ka sa iyong sariling Dashboard.';

        if ($role === 'admin') {
            return redirect()->route('dashboard.Admin')->with('error', $message);
        } elseif ($role === 'peso staff') {
            return redirect()->route('dashboard.PesoStaff')->with('error', $message);
        } elseif ($role === 'skilled worker') {
            return redirect()->route('dashboard.SkilledWorker')->with('error', $message);
        } elseif ($role === 'residential') {
            return redirect()->route('dashboard.HouseholdClient')->with('error', $message);
        } else {
            return redirect()->route('Login')->with('error', 'Mangyaring mag-login upang makapasok sa iyong dashboard.');
        }
    }
}