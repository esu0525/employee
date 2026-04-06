<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('auth_user_id')) {
            // Check if user is authenticated via native Laravel auth (Remember Me cookie)
            if (auth()->check()) {
                $user = auth()->user();
                // Restore session markers from the remember_me cookie authentication
                session([
                    'auth_user_id'    => $user->id,
                    'auth_user_name'  => $user->getNameAttribute(),
                    'auth_user_email' => $user->email,
                    'auth_user_role'  => $user->role,
                    'keep_logged_in'  => true, // Restoration happens via Remember Me cookie
                ]);
                
                if ($user->profile_picture) {
                    session(['welcome_avatar' => $user->profile_picture]);
                }

                // CRITICAL: Force save session immediately so it persists for subsequent AJAX/Heartbeat calls
                session()->save();
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Session expired.', 'redirect' => route('login')], 401);
                }
                return redirect()->route('login')->with('error', 'Please login to access this page.');
            }
        }

        // If "Keep me Login" is active, ensure Laravel and PHP configurations reflect the long lifetime
        if (session('keep_logged_in')) {
            $oneYear = 525600; // minutes
            config(['session.lifetime' => $oneYear]);
            ini_set('session.gc_maxlifetime', $oneYear * 60);

            // We rely on Laravel's native 'remember_web_...' cookie and the SESSION_LIFETIME 
            // set in .env rather than manual cookie queuing which can clash with the session driver.
        }

        return $next($request);
    }
}
