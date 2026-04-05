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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'viewer', 'editor', 'coordinator'])],
            'permissions' => 'nullable|array'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
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
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['admin', 'viewer', 'editor', 'coordinator'])],
            'permissions' => 'nullable|array'
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
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
