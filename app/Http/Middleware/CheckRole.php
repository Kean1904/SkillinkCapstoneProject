<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. I-check muna kung naka-login (may session)
        if (!session()->has('user_id')) {
            return redirect()->route('Login')->with('error', 'Please log in to continue.');
        }

        // 2. I-check kung tugma ang role ng naka-login sa mga pinapayagang role dito
        $userRole = session('user_role');

        if (!in_array($userRole, $roles)) {
            // Hindi pinapayagan - ibalik sa sarili niyang dashboard
            return $this->redirectToOwnDashboard($userRole);
        }

        // 3. Ituloy ang request
        $response = $next($request);

        // 4. Idagdag ang no-cache headers (para sa back button issue)
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }

    private function redirectToOwnDashboard($role)
    {
        $roleLower = strtolower(trim($role));
        if ($roleLower === 'admin') {
            return redirect()->route('dashboard.Admin')->with('error', 'You are not authorized to access that page.');
        } elseif ($roleLower === 'peso staff' || $roleLower === 'staff') {
            return redirect()->route('dashboard.PesoStaff')->with('error', 'You are not authorized to access that page.');
        } elseif ($roleLower === 'skilled worker' || $roleLower === 'skilled') {
            return redirect()->route('dashboard.SkilledWorker')->with('error', 'You are not authorized to access that page.');
        } elseif ($roleLower === 'residential' || str_contains($roleLower, 'household') || str_contains($roleLower, 'client')) {
            return redirect()->route('dashboard.HouseholdClient')->with('error', 'You are not authorized to access that page.');
        } else {
            return redirect()->route('Login');
        }
    }
}