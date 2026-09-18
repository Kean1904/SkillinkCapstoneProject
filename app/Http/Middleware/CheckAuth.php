<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. I-check kung naka-login (may session)
        if (!session()->has('user_id')) {
            return redirect()->route('Login')->with('error', 'Please log in to continue.');
        }

        // 2. Ituloy ang request
        $response = $next($request);

        // 3. Idagdag ang mga header para hindi ma-cache ng browser ang page
        // Ito ang solusyon sa "Back button" issue
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}