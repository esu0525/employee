<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * List all accounts
     */
    public function index()
    {
        $users = User::all();

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
        
        return view('admin.users.index', compact('users', 'available_permissions'));
    }

    /**
     * Store a new account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'viewer', 'editor', 'coordinator'])],
            'permissions' => 'nullable|array'
        ]);

        $hashedEmail = hash('sha256', strtolower(trim($validated['email'])));
        if (User::where('email_hash', $hashedEmail)->exists()) {
            return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'email_hash' => $hashedEmail,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'permissions' => in_array($validated['role'], ['admin', 'coordinator']) ? [] : ($validated['permissions'] ?? [])
        ]);

        \App\Models\ActivityLog::log('create', 'accounts', 'Created new system account for ' . $user->name);

        return redirect()->back()->with('success', 'User account created successfully.');
    }

    /**
     * Update an existing account
     */
    public function update(Request $request, $id)
    {
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

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'email_hash' => $hashedEmail,
            'role' => $validated['role'],
            'permissions' => in_array($validated['role'], ['admin', 'coordinator']) ? [] : ($validated['permissions'] ?? [])
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        \App\Models\ActivityLog::log('edit', 'accounts', 'Updated system account details for ' . $user->name);

        return redirect()->back()->with('success', 'User account updated successfully.');
    }

    /**
     * Delete an account
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting self (using session since custom auth is used)
        if ($user->id == session('auth_user_id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        \App\Models\ActivityLog::log('delete', 'accounts', 'Deleted system account for ' . $userName);

        return redirect()->back()->with('success', 'User account deleted successfully.');
    }
}
