<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');

        $query = Employee::where('status', 'active');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('date_joined', 'desc')->get();

        $total_active = Employee::where('status', 'active')->count();
        $total_departments = Employee::where('status', 'active')->distinct('department')->count('department');
        $filtered_count = $employees->count();

        return view('index', compact(
            'employees',
            'search',
            'total_active',
            'total_departments',
            'filtered_count'
        ));
    }

    public function show(Request $request): View|RedirectResponse
    {
        $id = $request->query('id');
        if (!$id) {
            return redirect()->route('employees.index');
        }

        $employee = Employee::find($id);
        if (!$employee) {
            return redirect()->route('employees.index');
        }

        $documents = $employee->documents()->orderBy('upload_date', 'desc')->get();
        $doc_count = $documents->count();

        return view('employee-details', compact('employee', 'documents', 'doc_count'));
    }

    public function history(): RedirectResponse
    {
        return redirect()->route('employees.history-inactive');
    }

    public function historyInactive(Request $request): View
    {
        $search = $request->query('search', '');
        $query = Employee::where('status', 'inactive');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('status_date', 'desc')->get();
        return view('history-inactive', compact('employees', 'search'));
    }

    public function historyResign(Request $request): View
    {
        $search = $request->query('search', '');
        $query = Employee::where('status', 'resign');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('status_date', 'desc')->get();
        return view('history-resign', compact('employees', 'search'));
    }

    public function historyRetired(Request $request): View
    {
        $search = $request->query('search', '');
        $query = Employee::where('status', 'retired');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('status_date', 'desc')->get();
        return view('history-retired', compact('employees', 'search'));
    }

    public function historyTransfer(Request $request): View
    {
        $search = $request->query('search', '');
        $query = Employee::where('status', 'transfer');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('transfer_location', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('status_date', 'desc')->get();
        return view('history-transfer', compact('employees', 'search'));
    }

    public function requests(Request $request): View
    {
        $search = $request->query('search', '');
        $status_filter = $request->query('status', 'all');

        $query = EmployeeRequest::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('request_type', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status_filter !== 'all') {
            $query->where('status', $status_filter);
        }

        $requests = $query->orderBy('request_date', 'desc')->get();

        $all_requests = EmployeeRequest::count();
        $pending_count = EmployeeRequest::where('status', 'pending')->count();
        $approved_count = EmployeeRequest::where('status', 'approved')->count();
        $rejected_count = EmployeeRequest::where('status', 'rejected')->count();

        return view('requests', compact(
            'requests', 'search', 'status_filter',
            'all_requests', 'pending_count', 'approved_count', 'rejected_count'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'date_joined' => 'required|date',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        // Generate ID
        $lastEmployee = Employee::orderBy('id', 'desc')->first();
        if ($lastEmployee) {
            $lastId = intval(substr($lastEmployee->id, 3));
            $newId = 'EMP' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }
        else {
            $newId = 'EMP001';
        }

        $data['id'] = $newId;
        $data['status'] = 'active';

        Employee::create($data);

        return redirect()->route('employees.index')->with('success_message', "Employee $newId added successfully");
    }

    public function upload(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        if ($request->hasFile('documents')) {
            $current_count = $employee->documents()->count();
            $files = $request->file('documents');
            $upload_count = 0;

            foreach ($files as $file) {
                if ($current_count + $upload_count >= 5)
                    break;

                if ($file->getClientOriginalExtension() === 'pdf') {
                    $filename = time() . '_' . $upload_count . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $filename);

                    $employee->documents()->create([
                        'document_name' => $file->getClientOriginalName(),
                        'file_path' => 'uploads/' . $filename
                    ]);
                    $upload_count++;
                }
            }

            if ($upload_count > 0) {
                return back()->with('success_message', "$upload_count document(s) uploaded successfully");
            }
        }

        return back()->with('error_message', 'No valid PDF documents uploaded or limit reached');
    }

    public function deleteDoc($id): RedirectResponse
    {
        $doc = EmployeeDocument::findOrFail($id);
        $file_path = public_path($doc->file_path);

        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        $doc->delete();

        return back()->with('success_message', 'Document deleted successfully');
    }

    public function approveRequest($id): RedirectResponse
    {
        $request = EmployeeRequest::findOrFail($id);
        $request->update(['status' => 'approved']);
        return back()->with('success_message', 'Request approved successfully');
    }

    public function rejectRequest($id): RedirectResponse
    {
        $request = EmployeeRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);
        return back()->with('success_message', 'Request rejected successfully');
    }
}
