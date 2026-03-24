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
     * Display a specific user's logic (shared)
     */
    private function prepareProfileData($user)
    {
        if (!$user) return null;

        $available_permissions = [
            'view_employees' => 'View Employees',
            'edit_employees' => 'Edit Employees',
            'delete_employees' => 'Delete Employees',
            'manage_documents' => 'Manage Documents',
            'manage_requests' => 'Manage Requests',
            'manage_accounts' => 'Manage Accounts'
        ];

        // Fetch user activity logs (logins, views, edits, deletes, etc.)
        $logs = \App\Models\ActivityLog::where('user_id', $user->id)
                                    ->orderByDesc('created_at')
                                    ->limit(10)
                                    ->get();
                                    
        // Get the total number of logins (specifically)
        $totalLogins = \App\Models\ActivityLog::where('user_id', $user->id)
                                             ->where('action', 'login')
                                             ->count();
        
        // Logins per day for the last 7 days for the chart
        $loginsPerDay = \App\Models\ActivityLog::selectRaw('DATE(created_at) as date, count(*) as count')
                                             ->where('user_id', $user->id)
                                             ->where('action', 'login')
                                             ->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)')
                                             ->groupBy('date')
                                             ->get()
                                             ->pluck('count', 'date');
                                             
        $chartData = [];
        $chartLabels = [];
        $chartFullDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[]    = now()->subDays($i)->format('D');
            $chartFullDates[] = now()->subDays($i)->format('D, M j');
            $chartData[]      = $loginsPerDay[$date] ?? 0;
        }

        return compact('user', 'logs', 'totalLogins', 'chartLabels', 'chartFullDates', 'chartData', 'available_permissions');
    }

    /**
     * Admin updating an account from the profile page
     */
    public function updateAdmin(Request $request, $id)
    {
        $currentUser = $this->getUser();
        if (!$currentUser || $currentUser->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $user = \App\Models\User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'permissions' => 'nullable|array'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->permissions = ($validated['role'] === 'admin') ? [] : ($validated['permissions'] ?? []);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $changed = [];
        if($user->isDirty('name')) $changed[] = 'Name';
        if($user->isDirty('email')) $changed[] = 'Email';
        if($user->isDirty('role')) $changed[] = 'Role';
        if($user->isDirty('permissions')) $changed[] = 'Permissions';
        if(!empty($validated['password'])) $changed[] = 'Password';

        $user->save();

        $desc = 'Administratively updated account details for ' . $user->name;
        if(!empty($changed)) {
            $desc .= ' (Edited: ' . implode(', ', $changed) . ')';
        }

        \App\Models\ActivityLog::log('edit', 'profile', $desc);

        return redirect()->back()->with('success', 'User account updated successfully.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $user = $this->getUser();
        if (!$user) return redirect()->route('login');

        $data = $this->prepareProfileData($user);

        \App\Models\ActivityLog::log('edit', 'profile', 'Updated personal profile settings');

        return view('profile.edit', $data);
    }

    /**
     * View any user profile (Admin only)
     */
    public function show($id)
    {
        $currentUser = $this->getUser();
        if (!$currentUser || $currentUser->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $user = \App\Models\User::findOrFail($id);
        $data = $this->prepareProfileData($user);
        
        // Pass a flag indicating we are viewing someone else
        $data['isReadOnly'] = ($currentUser->id != $user->id);
        
        \App\Models\ActivityLog::log('view', 'profile', 'Viewed profile of ' . $user->name);

        return view('profile.edit', $data);
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
        $filename = time() . '_' . \Illuminate\Support\Str::slug($user->name) . '.' . $file->getClientOriginalExtension();
        $binaryContent = file_get_contents($file->getRealPath());
        
        // Move the file into public/assets/avatars
        $file->move(public_path('assets/avatars'), $filename);
        
        // Delete old avatar if exists and not default
        if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
            @unlink(public_path($user->profile_picture));
        }
        
        $user->profile_picture = 'assets/avatars/' . $filename;
        $user->profile_picture_content = $binaryContent;
        
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
