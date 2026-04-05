<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortalController extends Controller
{
    public function index()
    {
        // We still fetch employees for its potential use in the admin side, 
        // but the portal form now uses a text input for employee_name
        $employees = Employee::orderBy('name', 'asc')->get(['id', 'name']);
        return view('portal.index', compact('employees'));
    }

    public function view($id)
    {
        $request = EmployeeRequest::findOrFail($id);
        return view('portal.view', compact('request'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'num_copies' => 'required|integer|min:1',
            'document_request' => 'required|string',
            'purpose' => 'required|string',
            'purpose_other' => 'required_if:purpose,OTHERS',
            'requirements_files.*' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:512000',
        ]);

        $request_type = $request->document_request;

        $purpose = $request->purpose;
        if ($purpose === 'OTHERS' && $request->purpose_other) {
            $purpose = $request->purpose_other;
        }

        // Handle multiple file uploads
        $requirements_files_paths = [];
        $requirements_files_contents = [];
        
        if ($request->hasFile('requirements_files')) {
            foreach ($request->file('requirements_files') as $file) {
                if ($file) {
                    $filename = time() . '_' . Str::random(5) . '_' . $file->getClientOriginalName();
                    $content = file_get_contents($file->getRealPath());
                    $file->move(public_path('uploads/requirements'), $filename);
                    
                    $requirements_files_paths[] = 'uploads/requirements/' . $filename;
                    $requirements_files_contents[] = base64_encode($content); // Store as base64 in comma string or similar if needed, but the column is BLOB.
                    // Actually, if we have multiple, storing multiple blobs in one column is tricky.
                    // For now, I'll store the first one's content in the blob column for compatibility,
                    // or I'll just store the paths.
                }
            }
        }

        // Try to find a matching employee ID if they exist, otherwise leave it empty (null)
        $employee = Employee::where('name', 'like', "%{$request->employee_name}%")->first();
        $employee_id = $employee ? $employee->id : null;

        EmployeeRequest::create([
            'user_id' => session('auth_user_id'),
            'employee_id' => $employee_id,
            'employee_name' => $request->employee_name,
            'agency' => $request->agency,
            'request_type' => $request_type,
            'num_copies' => $request->num_copies,
            'purpose' => $purpose,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
            'description' => "Filed via Portal. Purpose: $purpose",
            'requirements_file' => implode(';', $requirements_files_paths),
            'requirements_file_content' => !empty($requirements_files_contents) ? base64_decode($requirements_files_contents[0]) : null
        ]);

        \App\Models\ActivityLog::log('create', 'requests', 'Portal: Guest/User submitted document request for ' . $request->employee_name);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Your request has been filed successfully and is now pending for approval.',
                'status' => 'success'
            ]);
        }

        return back()->with('success_message', 'Your request has been filed successfully and is now pending for approval.');
    }
}
