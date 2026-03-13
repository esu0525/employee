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

        $employees = $query->orderBy('last_name', 'asc')->orderBy('first_name', 'asc')->get();

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

    public function masterlist(Request $request)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'name'); // 'name' or 'box'

        $query = Employee::where('status', 'active');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('box_number', 'like', "%{$search}%");
            });
        }

        if ($sort === 'position') {
            $query->orderBy('position', 'asc')->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        } else {
            $query->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        }

        $employees = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('partials.masterlist-table', compact('employees', 'sort'))->render();
        }

        return view('masterlist', compact('employees', 'search', 'sort'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $importCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue; // Skip empty rows

            $lastName = trim($row[0] ?? '');
            $firstName = trim($row[1] ?? '');
            $middleName = trim($row[2] ?? '');
            $position = trim($row[3] ?? 'Employee');
            $office = trim($row[4] ?? 'Unknown');

            $mi = !empty($middleName) ? ' ' . strtoupper(substr($middleName, 0, 1)) . '.' : '';
            $fullName = "{$firstName}{$mi} {$lastName}";

            // Generate ID
            $lastEmployee = Employee::orderBy('id', 'desc')->first();
            $lastIdNum = $lastEmployee ? intval(substr($lastEmployee->id, 3)) : 0;
            $newId = 'EMP' . str_pad($lastIdNum + 1, 3, '0', STR_PAD_LEFT);

            Employee::create([
                'id' => $newId,
                'name' => $fullName,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'position' => $position,
                'department' => $office,
                'status' => 'active',
                'date_joined' => now()
            ]);

            $importCount++;
        }
        fclose($handle);

        return back()->with('success', "$importCount employees imported successfully!");
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

        $documents = $employee->documents()->orderBy('created_at', 'desc')->get();
        $doc_count = $documents->count();

        return view('employee-details', compact('employee', 'documents', 'doc_count'));
    }

    public function history(Request $request): View
    {
        $search = $request->query('search', '');
        
        // Base query for each status
        $baseQuery = function($status) use ($search) {
            $query = Employee::where('status', $status);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('transfer_location', 'like', "%{$search}%");
                });
            }
            return $query->orderBy('status_date', 'desc')->get();
        };

        $resign = $baseQuery('resign');
        $retired = $baseQuery('retired');
        $transfer = $baseQuery('transfer');

        return view('history', compact('resign', 'retired', 'transfer', 'search'));
    }

    public function requests(Request $request): View
    {
        $search = $request->query('search', '');
        $status_filter = 'all';

        // Only show pending requests — approved ones go to the Approved List
        $query = EmployeeRequest::where('status', 'pending');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('request_type', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('request_date', 'desc')->get();

        $all_requests = EmployeeRequest::count();
        $pending_count = EmployeeRequest::where('status', 'pending')->count();
        $approved_count = EmployeeRequest::where('status', 'approved')->count();
        $rejected_count = EmployeeRequest::where('status', 'rejected')->count();

        $employees = Employee::where('status', 'active')->orderBy('name', 'asc')->get();

        return view('requests', compact(
            'requests', 'search', 'status_filter',
            'all_requests', 'pending_count', 'approved_count', 'rejected_count',
            'employees'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'box_number' => 'nullable|string|max:50',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'sex' => 'required|string|in:Male,Female',
            'address' => 'required|string|max:500',
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
        $data['date_joined'] = now()->toDateString();

        Employee::create($data);

        return redirect()->route('employees.index')->with('success_message', "Employee {$data['name']} has been successfully saved!");
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'status' => 'required|string|in:active,inactive,resign,retired,transfer',
            'so_no' => 'required_if:status,transfer|nullable|string|max:255',
            'transfer_to' => 'required_if:status,transfer|nullable|string|max:255',
            'effective_date' => 'required|date',
            'retirement_under' => 'required_if:status,retired|nullable|string|max:255',
        ]);

        $employee->update([
            'status' => $request->status,
            'status_date' => now()->toDateString(),
            'so_no' => $request->so_no,
            'transfer_to' => $request->transfer_to,
            'effective_date' => $request->effective_date,
            'retirement_under' => $request->retirement_under,
        ]);

        return redirect()->route('employees.index')->with('success_modal', [
            'title' => 'Status Updated!',
            'message' => "Employee has been successfully moved to " . ucfirst($request->status) . " list."
        ]);
    }

    public function upload(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        if ($employee->status !== 'active') {
            return back()->with('error_message', 'Documents cannot be uploaded for inactive or history employees.');
        }

        $request->validate([
            'category' => 'nullable|string|max:50',
            'documents.*' => 'required|file|mimes:pdf,jpeg,png,jpg,docx,xlsx,doc|max:10240',
        ]);

        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            $upload_count = 0;
            $category = $request->input('category', 'UNCATEGORIZED');

            foreach ($files as $file) {
                $filename = time() . '_' . $upload_count . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);

                $employee->documents()->create([
                    'document_name' => $file->getClientOriginalName(),
                    'file_path' => 'uploads/' . $filename,
                    'category' => $category
                ]);
                $upload_count++;
            }

            if ($upload_count > 0) {
                return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'documents'])
                    ->with('success_message', "$upload_count document(s) uploaded successfully to $category");
            }
        }

        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'documents'])
            ->with('error_message', 'No valid documents uploaded');
    }

    public function deleteDoc($id): RedirectResponse
    {
        $doc = EmployeeDocument::findOrFail($id);
        $employee = $doc->employee;

        if ($employee && $employee->status !== 'active') {
            return back()->with('error_message', 'Documents cannot be deleted for inactive or history employees.');
        }

        $file_path = public_path($doc->file_path);

        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        $doc->delete();

        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'documents'])
            ->with('success_message', 'Document deleted successfully');
    }

    public function approveRequest($id): RedirectResponse
    {
        $request = EmployeeRequest::findOrFail($id);
        $request->update(['status' => 'approved']);
        return redirect()->route('employees.requests')->with('success_message', 'Request approved and moved to Approved List');
    }

    public function rejectRequest($id): RedirectResponse
    {
        $request = EmployeeRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);
        return redirect()->route('employees.requests')->with('success_message', 'Request has been rejected');
    }

    public function submitRequest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => 'required|string|exists:employees,id',
            'request_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        EmployeeRequest::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'request_type' => $data['request_type'],
            'request_date' => now()->toDateString(),
            'status' => 'pending',
            'description' => $data['description'] ?? '',
        ]);

        return redirect()->route('employees.requests')->with('success_message', 'Document request submitted successfully!');
    }

    public function approvedList(Request $request): View
    {
        $search = $request->query('search', '');

        $query = EmployeeRequest::where('status', 'approved');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('request_type', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $approved_requests = $query->orderBy('updated_at', 'desc')->get();

        return view('approved-list', compact('approved_requests', 'search'));
    }
    public function update(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $data = $request->validate([
            'last_name' => 'nullable|string|max:100',
            'first_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'so_number' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'sex' => 'nullable|string|in:Male,Female',
            'marital_status' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:100',
            'blood_type' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        if ($request->filled('first_name') && $request->filled('last_name')) {
            $mi = !empty($data['middle_name']) ? ' ' . strtoupper(substr($data['middle_name'], 0, 1)) . '.' : '';
            $data['name'] = "{$data['first_name']}{$mi} {$data['last_name']}";
        } else {
            // Keep the current name if names aren't being updated
            unset($data['name']);
            unset($data['first_name']);
            unset($data['last_name']);
            unset($data['middle_name']);
        }

        $employee->update($data);

        $tab = $request->input('position') ? 'work' : 'personal';
        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => $tab])
            ->with('success_message', 'Employee details updated successfully.');
    }
    public function updateAvatar(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);

            // Delete old avatar if exists
            if ($employee->profile_picture && file_exists(public_path($employee->profile_picture))) {
                @unlink(public_path($employee->profile_picture));
            }

            $employee->update([
                'profile_picture' => 'uploads/avatars/' . $filename
            ]);
        }

        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'personal'])
            ->with('success_message', 'Profile picture updated successfully.');
    }
}
