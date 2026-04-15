<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\EmailChangeVerification;
use App\Mail\VerifyEmailChange;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    /**
     * Get the currently authenticated user from session.
     */
    private function getUser()
    {
        return User::find(session('auth_user_id'));
    }

    /**
     * Display a specific user's logic (shared)
     */
    private function prepareProfileData($user)
    {
        if (!$user) return null;

        $available_permissions = [
            'view_masterlist'  => 'View Masterlist',
            'view_archive'     => 'View Archive',
            'view_requests'    => 'View Requests Center',
            'edit_masterlist'  => 'Edit Masterlist (Edit, Export, Status, Add)',
            'edit_archive'     => 'Edit Archive (Edit, Export, Upload)',
            'edit_report'      => 'Edit Report',
            'edit_requests'    => 'Manage Requests (Approve/Reject)',
            'manage_documents' => 'Manage Documents',
            'manage_accounts'  => 'Manage Accounts'
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

    public function updateAdmin(Request $request, $id)
    {
        $currentUser = $this->getUser();
        if (!$currentUser || $currentUser->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['admin', 'viewer', 'editor', 'coordinator'])],
            'permissions' => 'nullable|array'
        ]);

        $hashedEmail = hash('sha256', strtolower(trim($validated['email'])));
        if (User::where('email_hash', $hashedEmail)->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        }

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->email_hash = $hashedEmail;
        $user->role = $validated['role'];
        $user->permissions = in_array($validated['role'], ['admin', 'coordinator']) ? [] : ($validated['permissions'] ?? []);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        \App\Models\ActivityLog::log('edit', 'profile', 'Administratively updated account details for ' . $user->name . ' (Direct update by Admin)');

        $user->save();

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

        $user = User::findOrFail($id);
        $data = $this->prepareProfileData($user);
        
        // Pass a flag indicating we are viewing someone else
        $data['isReadOnly'] = ($currentUser->id != $user->id);
        
        \App\Models\ActivityLog::log('view', 'profile', 'Viewed profile of ' . $user->name);

        return view('profile.edit', $data);
    }

    public function update(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return redirect()->route('login');
        
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
        ];

        if ($user->role === 'admin') {
            $rules['first_name'] = 'required|string|max:255';
            $rules['last_name'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $hashedEmail = hash('sha256', strtolower(trim($request->email)));
        if (User::where('email_hash', $hashedEmail)->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        }

        if ($user->role === 'admin' && $request->has('first_name')) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->save();
            session(['auth_user_name' => $user->name]);
        }

        // Note: Regular users can no longer edit their Name as per requirements.
        // It resides as readonly in UI and ignored here.

        $newEmail = $request->email;

        // If email is different, initiate verification
        if ($newEmail !== $user->email) {
            // Generate token
            $token = Str::random(60);
            
            // Store verification request
            EmailChangeVerification::create([
                'user_id' => $user->id,
                'old_email' => $user->email,
                'new_email' => $newEmail,
                'token' => $token,
                'expires_at' => now()->addHours(24)
            ]);

            // Send Email
            Mail::to($newEmail)->send(new VerifyEmailChange($user->name, $newEmail, $token));

            \App\Models\ActivityLog::log('edit', 'profile', 'Initiated email address change from ' . $user->email . ' to ' . $newEmail);

            return redirect()->back()->with('success', 'A verification email has been sent to ' . $newEmail . '. Please verify to complete the change.');
        }
        
        return redirect()->back()->with('info', 'No changes were made.');
    }

    /**
     * Verify the new email via token.
     */
    public function verifyEmail($token)
    {
        $verification = EmailChangeVerification::where('token', $token)->first();

        if (!$verification || $verification->isExpired()) {
            return redirect()->route('login')->with('error', 'Token is invalid or has expired.');
        }

        $user = User::find($verification->user_id);
        if ($user) {
            $oldEmail = $user->email;
            $user->email = $verification->new_email;
            $user->email_hash = hash('sha256', strtolower(trim($verification->new_email)));
            $user->save();

            \App\Models\ActivityLog::log('edit', 'profile', 'Verified new email address change from ' . $oldEmail . ' to ' . $user->email);

            // Clean up all verifications for this user
            EmailChangeVerification::where('user_id', $user->id)->delete();

            return redirect()->route('profile.edit')->with('success', 'Email address has been successfully verified and updated.');
        }

        return redirect()->route('login')->with('error', 'User not found.');
    }
    
    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $currentUser = $this->getUser();
        if (!$currentUser) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        
        $targetUserId = $request->input('user_id', $currentUser->id);
        if ($targetUserId != $currentUser->id && $currentUser->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($targetUserId);

        $request->validate([
            'avatar' => 'required|string' // We expect base64 from cropper
        ]);
        
        $imgData = $request->avatar;
        if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
            $imgData = substr($imgData, strpos($imgData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                return response()->json(['success' => false, 'message' => 'Invalid image type'], 422);
            }

            $imgData = base64_decode($imgData);

            if ($imgData === false) {
                return response()->json(['success' => false, 'message' => 'Base64 decode failed'], 422);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid image data'], 422);
        }

        $filename = time() . '_' . Str::slug($user->name) . '.' . $type;
        $path = 'assets/avatars/' . $filename;
        
        if (!file_exists(public_path('assets/avatars'))) {
            mkdir(public_path('assets/avatars'), 0755, true);
        }

        file_put_contents(public_path($path), $imgData);
        
        // Delete old avatar if exists and not default
        if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
            @unlink(public_path($user->profile_picture));
        }
        
        $user->profile_picture = $path;
        $user->save();

        // Update session avatar ONLY if updating self
        if ($targetUserId == $currentUser->id) {
            session(['welcome_avatar' => $path]);
        }
        
        \App\Models\ActivityLog::log('edit', 'profile', 'Updated profile picture for ' . ($targetUserId == $currentUser->id ? 'self' : $user->name) . ' (cropped)');

        return response()->json(['success' => true, 'message' => 'Profile picture updated successfully.', 'avatar' => asset($path)]);
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request)
    {
        $currentUser = $this->getUser();
        if (!$currentUser) return redirect()->route('login');

        $targetUserId = $request->input('user_id', $currentUser->id);
        if ($targetUserId != $currentUser->id && $currentUser->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $user = User::findOrFail($targetUserId);

        if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
            @unlink(public_path($user->profile_picture));
        }

        $user->profile_picture = null;
        $user->save();

        if ($targetUserId == $currentUser->id) {
            session()->forget('welcome_avatar');
        }

        \App\Models\ActivityLog::log('delete', 'profile', 'Deleted profile picture for ' . ($targetUserId == $currentUser->id ? 'self' : $user->name));

        return redirect()->back()->with('success', 'Profile picture deleted successfully.');
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

        \App\Models\ActivityLog::log('edit', 'profile', 'Updated personal account password');

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
