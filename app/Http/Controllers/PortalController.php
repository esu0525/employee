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
            'doc_types' => 'required|array',
            'purpose' => 'required|string',
            'purpose_other' => 'required_if:purpose,OTHERS',
            'requirements_file' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',
        ]);

        // Combine doc types
        $request_type = implode(', ', $request->doc_types);
        if (in_array('OTHERS', $request->doc_types) && $request->doc_others) {
            $request_type .= " (" . $request->doc_others . ")";
        }

        $purpose = $request->purpose;
        if ($purpose === 'OTHERS' && $request->purpose_other) {
            $purpose = $request->purpose_other;
        }

        // Handle file upload
        $requirements_file = null;
        if ($request->hasFile('requirements_file')) {
            $file = $request->file('requirements_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/requirements'), $filename);
            $requirements_file = 'uploads/requirements/' . $filename;
        }

        // Try to find a matching employee ID if they exist, otherwise leave it empty or generic
        $employee = Employee::where('name', 'like', "%{$request->employee_name}%")->first();
        $employee_id = $employee ? $employee->id : 'PORTAL_USER';

        EmployeeRequest::create([
            'employee_id' => $employee_id,
            'employee_name' => $request->employee_name,
            'agency' => $request->agency,
            'request_type' => $request_type,
            'num_copies' => $request->num_copies,
            'purpose' => $purpose,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
            'description' => "Filed via Portal. Purpose: $purpose",
            'requirements_file' => $requirements_file
        ]);

        return back()->with('success_message', 'Your request has been filed successfully and is now pending for approval.');
    }
}
