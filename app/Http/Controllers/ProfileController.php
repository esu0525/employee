<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Get the currently authenticated user from session.
     */
    private function getUser()
    {
        return \App\Models\User::find(session('auth_user_id'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $user = $this->getUser();
        if (!$user) return redirect()->route('login');

        // Fetch user login logs
        $logs = \App\Models\LoginLog::where('user_id', $user->id)
                                    ->orderByDesc('created_at')
                                    ->limit(10)
                                    ->get();
                                    
        // Get the total number of logins
        $totalLogins = \App\Models\LoginLog::where('user_id', $user->id)->count();
        
        // Logins per day for the last 7 days for the chart
        $loginsPerDay = \App\Models\LoginLog::selectRaw('DATE(created_at) as date, count(*) as count')
                                             ->where('user_id', $user->id)
                                             ->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)')
                                             ->groupBy('date')
                                             ->get()
                                             ->pluck('count', 'date');
                                             
        $chartData = [];
        $chartLabels = [];
        $chartFullDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[]    = now()->subDays($i)->format('D');               // Mon, Tue…
            $chartFullDates[] = now()->subDays($i)->format('D, M j');          // Mon, Mar 10
            $chartData[]      = $loginsPerDay[$date] ?? 0;
        }

        return view('profile.edit', compact('user', 'logs', 'totalLogins', 'chartLabels', 'chartFullDates', 'chartData'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return redirect()->route('login');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        session(['auth_user_name' => $user->name]);
        
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return redirect()->route('login');
        
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $file = $request->file('avatar');
        $filename = time() . '_' . Str::slug($user->name) . '.' . $file->getClientOriginalExtension();
        
        // Move the file into public/assets/avatars
        $file->move(public_path('assets/avatars'), $filename);
        
        // Delete old avatar if exists and not default
        if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
            @unlink(public_path($user->profile_picture));
        }
        
        $user->profile_picture = 'assets/avatars/' . $filename;
        
        // Also update session avatar since app.blade.php uses it
        session(['welcome_avatar' => 'assets/avatars/' . $filename]);
        
        $user->save();

        return redirect()->back()->with('success', 'Profile picture updated successfully.');
    }
    
    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            if ($request->expectsJson()) return response()->json(['error' => 'Unauthenticated.'], 401);
            return redirect()->route('login');
        }

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['current_password' => ['The provided password does not match your current password.']]], 422);
            }
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Password changed successfully.']);
        }

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    /**
     * Check if the provided current password is correct (AJAX).
     */
    public function checkPassword(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['valid' => false], 401);

        $valid = Hash::check($request->password, $user->password);
        return response()->json(['valid' => $valid]);
    }
}
