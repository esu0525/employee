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
    public function dashboard(): View
    {
        $total_employees = Employee::count();
        $total_active = Employee::where('status', 'active')->count();
        
        $pending_requests = EmployeeRequest::where('status', 'pending')->count();
        $recent_requests = EmployeeRequest::orderBy('request_date', 'desc')->take(5)->get();
        
        $history_count = Employee::whereIn('status', ['resign', 'retired', 'transfer', 'others'])->count();
        $recent_activity = Employee::whereIn('status', ['resign', 'retired', 'transfer', 'others'])
            ->orderBy('status_date', 'desc')
            ->take(5)
            ->get();

        // Recruitment Trend (Last 6 months)
        $recruitment_stats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Employee::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $recruitment_stats[] = [
                'month' => $month->format('M'),
                'count' => $count
            ];
        }

        $my_pending_requests = EmployeeRequest::where('user_id', session('auth_user_id'))
            ->where('status', 'pending')
            ->count();

        $status_stats = [
            'active' => $total_active,
            'resign' => Employee::where('status', 'resign')->count(),
            'retired' => Employee::where('status', 'retired')->count(),
            'transfer' => Employee::where('status', 'transfer')->count(),
            'others'   => Employee::where('status', 'others')->count(),
        ];

        return view('dashboard', compact(
            'total_employees',
            'total_active',
            'pending_requests',
            'my_pending_requests',
            'recent_requests',
            'history_count',
            'recent_activity',
            'recruitment_stats',
            'status_stats'
        ));
    }

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function addEmployee(): View
    {
        return view('add-employee');
    }

    public function masterlist(Request $request)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'name'); // 'name' or 'position'

        $query = Employee::where('status', 'active');

        if (!empty($search)) {
            $terms = explode(' ', $search);
            $query->where(function ($q) use ($search, $terms) {
                // Try whole string matches first on single fields
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('agency', 'like', "%{$search}%");

                // Try term by term matching for multi-word full names
                if (count($terms) > 1) {
                    $q->orWhere(function ($sq) use ($terms) {
                        foreach ($terms as $term) {
                            $sq->where(function($qq) use ($term) {
                                $qq->where('first_name', 'like', "%{$term}%")
                                   ->orWhere('last_name', 'like', "%{$term}%")
                                   ->orWhere('middle_name', 'like', "%{$term}%")
                                   ->orWhere('name', 'like', "%{$term}%");
                            });
                        }
                    });
                }
            });
        }

        if ($sort === 'position') {
            $query->orderBy('position', 'asc')->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        } else {
            $query->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        }

        $employees = $query->paginate(20)->withQueryString();

        // Summary Statistics for Masterlist
        $total_active = Employee::where('status', 'active')->count();
        $newly_joined = Employee::where('status', 'active')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($request->ajax()) {
            return view('partials.masterlist-table', compact('employees', 'sort'))->render();
        }

        \App\Models\ActivityLog::log('view', 'masterlist', 'Accessed the employee masterlist');

        return view('masterlist', compact(
            'employees', 
            'search', 
            'sort', 
            'total_active', 
            'newly_joined'
        ));
    }

    public function allEmployeesJson(Request $request)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'name');

        $query = Employee::where('status', 'active');

        if (!empty($search)) {
            $terms = explode(' ', $search);
            $query->where(function ($q) use ($search, $terms) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('agency', 'like', "%{$search}%");

                if (count($terms) > 1) {
                    $q->orWhere(function ($sq) use ($terms) {
                        foreach ($terms as $term) {
                            $sq->where(function($qq) use ($term) {
                                $qq->where('first_name', 'like', "%{$term}%")
                                   ->orWhere('last_name', 'like', "%{$term}%")
                                   ->orWhere('middle_name', 'like', "%{$term}%")
                                   ->orWhere('name', 'like', "%{$term}%");
                            });
                        }
                    });
                }
            });
        }

        if ($sort === 'position') {
            $query->orderBy('position', 'asc')->orderBy('last_name', 'asc');
        } else {
            $query->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        }

        $employees = $query->get(['id', 'name', 'position', 'agency', 'category', 'employment_status', 'salary_grade', 'level_of_position', 'sex', 'date_joined']);

        \App\Models\ActivityLog::log('export', 'masterlist', 'Exported masterlist data (JSON fetch for client-side generation). Search: ' . ($search ?: 'None'));
            
        return response()->json($employees);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        $isSeparationList = false;
        
        if ($header) {
            $headerString = implode(',', $header);
            if (str_contains(strtolower($headerString), 'remark') || str_contains(strtolower($headerString), 'separation')) {
                $isSeparationList = true;
            }
        }

        // Get the latest ID numeric part once before the loop
        $maxEmployee = Employee::where('id', 'LIKE', 'EMP%')
            ->selectRaw('MAX(CAST(SUBSTRING(id, 4) AS UNSIGNED)) as max_num')
            ->first();
        
        $nextIdNum = ($maxEmployee?->max_num ?? 0) + 1;

        $importCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) && (count($row) < 2 || empty($row[1]))) continue; 

            $col0 = trim($row[0] ?? '');
            $col1 = trim($row[1] ?? '');
            $col2 = trim($row[2] ?? '');
            $col3 = trim($row[3] ?? '');
            $col4 = trim($row[4] ?? '');
            $col5 = trim($row[5] ?? '');

            $lastName = '';
            $firstName = '';
            $middleName = '';
            $suffix = '';
            $position = 'Employee';
            $office = 'Unknown';
            $sex = 'Unknown';
            $status = 'active';
            $status_date = null;
            $status_specify = null;
            $so_no = null;
            $effective_date = null;

            if ($isSeparationList) {
                // Name, Date of Separation, SCHOOL, Remarks, S.O No.
                // Parse Name (col0)
                if (str_contains($col0, ',')) {
                    $parsed = $this->parseComplexName($col0);
                    $lastName = $parsed['last'];
                    $firstName = $parsed['first'];
                    $middleName = $parsed['middle'];
                    $suffix = $parsed['suffix'];
                } else {
                    // Try space split
                    $nameParts = explode(' ', $col0);
                    if (count($nameParts) >= 2) {
                        $lastName = array_pop($nameParts);
                        $firstName = implode(' ', $nameParts);
                    } else {
                        $lastName = $col0;
                        $firstName = 'Unknown';
                    }
                }
                
                // Date (col1)
                if ($col1 && $col1 !== '-') {
                    try {
                        $effective_date = \Carbon\Carbon::parse($col1)->format('Y-m-d');
                        $status_date = $effective_date;
                    } catch (\Exception $e) {}
                }

                $office = $col2 ?: 'Unknown';
                $remarks = strtolower($col3 ?: '');
                $so_no = $col4 ?: null;
                $status_specify = $col3 ?: null;

                // Category Logic
                if (str_contains($remarks, 'transfer')) {
                    $status = 'transfer';
                } elseif (str_contains($remarks, 'retire')) {
                    $status = 'retired';
                } elseif (str_contains($remarks, 'resign')) {
                    $status = 'resign';
                } else {
                    $status = 'others';
                }
            } else {
                $position = $col3 ?: 'Employee';
                $office = $col4 ?: 'Unknown';
                $sex = $col5 ?: 'Unknown';

                if (str_contains($col0, ',')) {
                    $parsed = $this->parseComplexName($col0);
                    $lastName = $parsed['last'];
                    $firstName = $parsed['first'];
                    $middleName = $parsed['middle'];
                    $suffix = $parsed['suffix'];
                } else if (!empty($col0) && empty($col1)) {
                    $nameParts = explode(' ', $col0);
                    if (count($nameParts) >= 2) {
                        $lastName = array_pop($nameParts);
                        $firstName = implode(' ', $nameParts);
                    } else {
                        $lastName = $col0;
                        $firstName = 'Unknown';
                    }
                } else {
                    $lastName = $col0;
                    $firstName = $col1;
                    $middleName = $col2;
                    if (str_contains($firstName ?? '', ',')) {
                        $parsed = $this->parseComplexName($lastName . ', ' . $firstName);
                        $lastName = $parsed['last'];
                        $firstName = $parsed['first'];
                        $middleName = $parsed['middle'];
                        $suffix = $parsed['suffix'];
                    }
                }
            }

            $mi = '';
            if (!empty($middleName)) {
                $rawMi = trim(str_replace('.', '', $middleName));
                if (!empty($rawMi)) {
                    $mi = strtoupper(substr($rawMi, 0, 1)) . '.';
                }
            }

            $dbDisplayName = $lastName . ', ' . $firstName;
            if ($mi) $dbDisplayName .= ' ' . $mi;
            if ($suffix) $dbDisplayName .= ' ' . $suffix;

            $newId = 'EMP' . str_pad($nextIdNum, 3, '0', STR_PAD_LEFT);

            Employee::create([
                'id' => $newId,
                'name' => trim($dbDisplayName),
                'last_name' => trim($lastName),
                'first_name' => trim($firstName),
                'middle_name' => trim($middleName), 
                'suffix' => trim($suffix),
                'position' => $position,
                'agency' => $office,
                'sex' => $sex,
                'status' => $status,
                'status_date' => $status_date,
                'effective_date' => $effective_date,
                'status_specify' => $status_specify,
                'so_no' => $so_no,
                'date_joined' => now()
            ]);

            $nextIdNum++;
            $importCount++;
        }

        fclose($handle);

        return back()->with('success', "$importCount employees imported successfully!");
    }

    private function parseComplexName($fullName)
    {
        $parts = explode(',', $fullName);
        $last = trim($parts[0] ?? '');
        $rest = trim($parts[1] ?? '');

        // Now split the rest (First Middle Suffix)
        $bits = explode(' ', $rest);
        $suffixes = ['JR.', 'SR.', 'III', 'IV', 'V', 'II', 'JR', 'SR', 'JR', 'SR', 'D.O.']; // simplified list
        
        $foundSuffix = '';
        $foundMiddle = '';
        $firstBits = [];

        foreach ($bits as $index => $bit) {
            $bitUpper = strtoupper(trim($bit, '. '));
            if (in_array($bitUpper, $suffixes) || in_array($bitUpper . '.', $suffixes)) {
                $foundSuffix = trim($bit);
                continue;
            }
            
            // Check if it's a middle initial (single letter or single letter with dot)
            if (strlen(trim($bit, '.')) == 1 && $index > 0) {
                $foundMiddle = trim($bit);
                continue;
            }

            $firstBits[] = $bit;
        }

        return [
            'last' => $last,
            'first' => implode(' ', $firstBits),
            'middle' => $foundMiddle,
            'suffix' => $foundSuffix
        ];
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
        $isArchived = ($employee->status !== 'active');

        $module = $isArchived ? 'archive' : 'masterlist';
        \App\Models\ActivityLog::log('view', $module, 'Viewed details for employee ' . $employee->name);

        return view('employee-details', compact('employee', 'documents', 'doc_count', 'isArchived'));
    }

    public function archive(Request $request)
    {
        $search = $request->query('search', '');
        $filter_year = $request->query('year');
        $filter_month = $request->query('month');
        $filter_date = $request->query('date');
        
        $sort = $request->query('sort', 'recent');

        // Base query for each status
        $baseQuery = function($status, $pageName) use ($search, $filter_year, $filter_month, $filter_date, $sort) {
            $query = Employee::where('status', $status);
            
            if ($filter_year) $query->whereYear('effective_date', $filter_year);
            if ($filter_month) $query->whereMonth('effective_date', $filter_month);
            if ($filter_date) $query->whereDate('effective_date', $filter_date);

            if (!empty($search)) {
                $terms = explode(' ', $search);
                $query->where(function ($q) use ($search, $terms) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('agency', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('transfer_location', 'like', "%{$search}%");

                    if (count($terms) > 1) {
                        $q->orWhere(function ($sq) use ($terms) {
                            foreach ($terms as $term) {
                                $sq->where(function($qq) use ($term) {
                                    $qq->where('first_name', 'like', "%{$term}%")
                                       ->orWhere('last_name', 'like', "%{$term}%")
                                       ->orWhere('name', 'like', "%{$term}%");
                                });
                            }
                        });
                    }
                });
            }

            if ($sort === 'name') {
                $query->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
            } elseif ($sort === 'sep_asc') {
                $query->orderBy('effective_date', 'asc');
            } elseif ($sort === 'archived_recent') {
                $query->orderBy('updated_at', 'desc');
            } elseif ($sort === 'archived_oldest') {
                $query->orderBy('updated_at', 'asc');
            } else { // 'sep_desc' or 'recent'
                $query->orderBy('effective_date', 'desc');
            }

            return $query->get();
        };

        $resign = $baseQuery('resign', 'resign_page');
        $retired = $baseQuery('retired', 'retired_page');
        $transfer = $baseQuery('transfer', 'transfer_page');
        $others = $baseQuery('others', 'others_page');

        $resign_count = Employee::where('status', 'resign')->count();
        $retired_count = Employee::where('status', 'retired')->count();
        $transfer_count = Employee::where('status', 'transfer')->count();
        $others_count = Employee::where('status', 'others')->count();

        $active_tab = $request->query('tab', 'resign');
        
        // Auto-locate search results across tabs
        if (!empty($search)) {
            $searchCounts = [
                'resign' => $resign->count(),
                'retired' => $retired->count(),
                'transfer' => $transfer->count(),
                'others' => $others->count(),
            ];
            
            // If the user's current tab has no search results, intelligently switch them to the first tab that has their result
            if (($searchCounts[$active_tab] ?? 0) === 0) {
                foreach ($searchCounts as $tab => $count) {
                    if ($count > 0) {
                        $active_tab = $tab;
                        break;
                    }
                }
            }
        }

        if ($request->ajax()) {
            return view('partials.archive-panels', compact(
                'resign', 'retired', 'transfer', 'others', 'search',
                'resign_count', 'retired_count', 'transfer_count', 'others_count',
                'active_tab'
            ))->render();
        }

        \App\Models\ActivityLog::log('view', 'archive', 'Accessed the archive module');

        return view('archive', compact(
            'resign', 'retired', 'transfer', 'others', 'search',
            'resign_count', 'retired_count', 'transfer_count', 'others_count',
            'active_tab'
        ));
    }

    public function archiveEmployeesJson(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        $tab = $request->query('tab'); // e.g. 'resign', 'retired', 'transfer', 'others'

        $query = Employee::whereIn('status', ['resign', 'retired', 'transfer', 'others']);

        if ($year && $year !== 'all') {
            $query->whereYear('effective_date', $year);
        }
        if ($month && $month !== 'all') {
            $query->whereMonth('effective_date', $month);
        }
        if ($tab && $tab !== 'all') {
            $query->where('status', $tab);
        }

        $employees = $query->orderBy('effective_date', 'desc')
            ->get(['id', 'name', 'position', 'agency', 'school', 'status', 'effective_date', 'status_specify', 'so_no', 'transfer_to', 'retirement_under', 'salary_grade', 'level_of_position', 'employment_status']);

        \App\Models\ActivityLog::log('export', 'archive', 'Exported archive data (JSON fetch). Filters: Year=' . ($year ?: 'All') . ', Month=' . ($month ?: 'All') . ', Tab=' . ($tab ?: 'All'));
            
        return response()->json($employees);
    }



    public function requests(Request $request): View|\Illuminate\Http\Response
    {
        $search = $request->query('search', '');
        $year = $request->query('year', '');
        $active_tab = $request->query('tab', 'pending');
        
        // Setup base queries with search and optional year filter
        $buildQuery = function ($status) use ($search, $year) {
            $query = EmployeeRequest::where('status', $status);
            
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_name', 'like', "%{$search}%")
                        ->orWhere('request_type', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }
            
            if (!empty($year)) {
                $query->whereYear('request_date', $year);
            }
            
            return $query;
        };

        // Execute queries with full collections for client-side pagination snappiness
        $requests = $buildQuery('pending')->orderBy('request_date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->orderBy('id', 'desc')
                                 ->get();
    
        $approved_requests = $buildQuery('approved')->orderBy('updated_at', 'desc')
                                           ->orderBy('id', 'desc')
                                           ->get();

        $all_requests = EmployeeRequest::count();
        $pending_count = EmployeeRequest::where('status', 'pending')->count();
        $approved_count = EmployeeRequest::where('status', 'approved')->count();
        $rejected_count = EmployeeRequest::where('status', 'rejected')->count();

        $employees = Employee::where('status', 'active')->orderBy('name', 'asc')->get();

        $viewData = compact(
            'requests', 'approved_requests', 'search', 'year', 'active_tab',
            'all_requests', 'pending_count', 'approved_count', 'rejected_count',
            'employees'
        );

        if ($request->ajax()) {
            return view('requests', $viewData);
        }

        return view('requests', $viewData);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'suffix' => 'nullable|string|max:50',
            'position' => 'required|string|max:100',
            'agency' => 'required|string|max:100',
            'category' => 'required|string|in:National,City',
            'employment_status' => 'required|string|in:Permanent,Contractual,Original',
            'salary_grade' => 'nullable|string|max:50',
            'level_of_position' => 'nullable|string|max:100',
            'so_number' => 'nullable|string|max:100',
            'date_of_birth' => 'required|date',
            'sex' => 'required|string|in:Male,Female',
            'civil_status' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'profile_picture' => 'nullable',
            'cropped_image' => 'nullable|string',
            'doc_items.*.classification' => 'required|string|max:100',
            'doc_items.*.files.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,docx,xlsx,doc|max:512000',
        ]);

        // Generate ID (Numeric-first sorting)
        $maxNum = (int)Employee::where('id', 'LIKE', 'EMP%')
            ->selectRaw('MAX(CAST(SUBSTRING(id, 4) AS UNSIGNED)) as max_num')
            ->value('max_num') ?? 0;
        $nextNum = $maxNum + 1;
        $newId = $nextNum >= 1000 ? 'EMP' . $nextNum : 'EMP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $data['id'] = $newId;
        $data['status'] = 'active';
        $data['date_joined'] = now()->toDateString();

        if ($request->filled('cropped_image')) {
            $imgData = $request->input('cropped_image');
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
            }
            $imgData = str_replace(' ', '+', $imgData);
            $decoded = base64_decode($imgData);
            $filename = time() . '_profile.jpg';
            
            // Still save to disk if needed, but primary is BLOB
            $path = public_path('uploads/avatars/' . $filename);
            if (!file_exists(public_path('uploads/avatars'))) {
                mkdir(public_path('uploads/avatars'), 0777, true);
            }
            file_put_contents($path, $decoded);
            $data['profile_picture'] = 'uploads/avatars/' . $filename;
        } elseif ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['profile_picture'] = 'uploads/avatars/' . $filename;
        }

        $employee = Employee::create($data);

        \App\Models\ActivityLog::log('create', 'masterlist', 'Created new employee record: ' . $employee->name);

        if ($request->has('doc_items')) {
            foreach ($request->doc_items as $index => $item) {
                $category = $item['classification'] ?? 'UNCATEGORIZED';
                if ($request->hasFile("doc_items.{$index}.files")) {
                    $files = $request->file("doc_items.{$index}.files");
                    foreach ($files as $fileIndex => $file) {
                        $filename = time() . "_{$index}_{$fileIndex}_" . $file->getClientOriginalName();
                        $file->move(public_path('uploads'), $filename);
                        $employee->documents()->create([
                            'document_name' => $file->getClientOriginalName(),
                            'file_path' => 'uploads/' . $filename,
                            'category' => $category
                        ]);
                    }
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => "Employee {$data['name']} has been successfully saved!",
                'status' => 'success',
                'redirect' => route('employees.add')
            ]);
        }

        return redirect()->route('employees.add')->with('success_message', "Employee {$data['name']} has been successfully saved!");
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'status' => 'required|string|in:transfer,retired,resign,others',
            'so_no' => 'nullable|string|max:255',
            'transfer_to' => 'nullable|string|max:255',
            'effective_date' => 'required|date',
            'status_specify' => 'nullable|string|max:255',
        ]);

        $employee->update([
            'status' => $request->status,
            'status_date' => now()->toDateString(),
            'so_no' => $request->so_no,
            'transfer_to' => $request->transfer_to,
            'effective_date' => $request->effective_date,
            'status_specify' => $request->status_specify,
            'retirement_under' => null, // Clear out any legacy data for this deprecated field
        ]);

        \App\Models\ActivityLog::log('edit', 'archive', 'Moved employee ' . $employee->name . ' to ' . strtoupper($request->status) . ' list');

        return redirect()->route('employees.masterlist')->with('success_modal', [
            'title' => 'Status Updated!',
            'message' => "Employee has been successfully moved to " . ucfirst($request->status) . " list."
        ]);
    }

    public function upload(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'category' => 'nullable|string|max:50',
            'documents.*' => 'required|file|mimes:pdf,jpeg,png,jpg,docx,xlsx,doc|max:512000',
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
                
                $module = in_array($employee->status, ['resign', 'retired', 'transfer', 'others']) ? 'archive' : 'masterlist';
                \App\Models\ActivityLog::log('upload', $module, 'Uploaded document: ' . $file->getClientOriginalName() . ' for ' . $employee->name);
                
                $upload_count++;
            }

            if ($upload_count > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => "$upload_count document(s) uploaded successfully to $category",
                        'status' => 'success'
                    ]);
                }
                return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'documents'])
                    ->with('success_message', "$upload_count document(s) uploaded successfully to $category");
            }
        }

        if ($request->ajax()) return response()->json(['message' => 'No valid documents uploaded.'], 400);
        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'documents'])
            ->with('error_message', 'No valid documents uploaded');
    }

    public function deleteDoc($id): RedirectResponse
    {
        $doc = EmployeeDocument::findOrFail($id);
        $employee = $doc->employee;

        $file_path = public_path($doc->file_path);

        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        $doc_name = $doc->document_name;
        $emp_name = $employee ? $employee->name : 'Unknown';
        $doc->delete();

        $module = ($employee && in_array($employee->status, ['resign', 'retired', 'transfer', 'others'])) ? 'archive' : 'masterlist';
        \App\Models\ActivityLog::log('delete', $module, 'Deleted document: ' . $doc_name . ' for ' . $emp_name);

        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'documents'])
            ->with('success_message', 'Document deleted successfully');
    }

    public function approveRequest(Request $request, $id): RedirectResponse
    {
        $req = EmployeeRequest::findOrFail($id);
        $req->update([
            'status' => 'approved',
            'prepared_by' => $request->input('prepared_by')
        ]);

        \App\Models\ActivityLog::log('edit', 'requests', 'Approved document request for ' . $req->employee_name . ' (Requested: ' . $req->request_type . ')');

        return redirect()->route('employees.requests')
            ->with('success_message', 'Request approved and moved to Approved List')
            ->with('new_approval', true);
    }

    public function rejectRequest($id): RedirectResponse
    {
        $request = EmployeeRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);
        
        \App\Models\ActivityLog::log('edit', 'requests', 'Rejected document request for ' . $request->employee_name . ' (Requested: ' . $request->request_type . ')');

        return redirect()->route('employees.requests')->with('success_message', 'Request has been rejected');
    }

    public function submitRequest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => 'required|string|exists:employees,id',
            'request_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'purpose' => 'required|string|max:255',
            'requirements_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,docx,xlsx,doc|max:10240',
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $purpose = $data['purpose'];

        $requirements_file = null;
        if ($request->hasFile('requirements_file')) {
            $file = $request->file('requirements_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            if (!file_exists(public_path('uploads/requirements'))) {
                mkdir(public_path('uploads/requirements'), 0777, true);
            }
            $file->move(public_path('uploads/requirements'), $filename);
            $requirements_file = 'uploads/requirements/' . $filename;
        }

        EmployeeRequest::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'agency' => $employee->agency,
            'request_type' => $data['request_type'],
            'request_date' => now()->toDateString(),
            'status' => 'pending',
            'description' => "Filed via Dashboard. Purpose: $purpose",
            'requirements_file' => $requirements_file,
        ]);

        \App\Models\ActivityLog::log('create', 'requests', 'Submitted document request for ' . $employee->name . ' (Requested: ' . $data['request_type'] . ')');

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
            'suffix' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'agency' => 'required|string|max:100',
            'category' => 'nullable|string|in:National,City',
            'employment_status' => 'nullable|string|in:Permanent,Contractual,Original',
            'salary_grade' => 'nullable|string|max:50',
            'level_of_position' => 'nullable|string|max:100',
            'so_number' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'sex' => 'nullable|string|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'effective_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,resign,retired,transfer,others',
            'so_no' => 'nullable|string|max:255',
            'status_specify' => 'nullable|string|max:1000',
            'transfer_to' => 'nullable|string|max:255',
        ]);

        if ($request->filled('first_name') && $request->filled('last_name')) {
            $mi = !empty($data['middle_name']) ? ' ' . strtoupper(substr($data['middle_name'], 0, 1)) . '.' : '';
            $suffix = !empty($data['suffix']) ? ' ' . $data['suffix'] : '';
            $data['name'] = "{$data['last_name']}, {$data['first_name']}{$mi}{$suffix}";
        } else {
            unset($data['name']);
            unset($data['first_name']);
            unset($data['last_name']);
            unset($data['middle_name']);
            unset($data['suffix']);
        }

        $changedFields = [];
        foreach ($data as $key => $value) {
            if (isset($employee->$key) && $employee->$key != $value) {
                $field = str_replace('_', ' ', $key);
                $changedFields[] = ucwords($field);
            }
        }
        
        $employee->update($data);

        $desc = 'Updated information for employee ' . $employee->name;
        if (!empty($changedFields)) {
            $desc .= ' (Edited: ' . implode(', ', array_slice($changedFields, 0, 3)) . (count($changedFields) > 3 ? '...' : '') . ')';
        }

        $module = in_array($employee->status, ['resign', 'retired', 'transfer', 'others']) ? 'archive' : 'masterlist';
        \App\Models\ActivityLog::log('edit', $module, $desc);

        $tab = $request->input('active_tab', 'personal');
        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => $tab])
            ->with('success_message', 'Employee details updated successfully.');
    }

    public function updateAvatar(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'profile_picture' => 'nullable|file|image|max:10240',
            'cropped_image' => 'nullable|string',
        ]);

        $filename = null;
        if ($request->filled('cropped_image')) {
            $imgData = $request->input('cropped_image');
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
            }
            $imgData = str_replace(' ', '+', $imgData);
            $decoded = base64_decode($imgData);
            $filename = time() . '_profile.jpg';
            $path = public_path('uploads/avatars/' . $filename);
            
            if (!file_exists(public_path('uploads/avatars'))) {
                mkdir(public_path('uploads/avatars'), 0777, true);
            }
            
            file_put_contents($path, $decoded);
        } elseif ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            if (!file_exists(public_path('uploads/avatars'))) {
                mkdir(public_path('uploads/avatars'), 0777, true);
            }
            $file->move(public_path('uploads/avatars'), $filename);
        }

        if ($filename) {
            // Delete old file if exists
            if ($employee->profile_picture && file_exists(public_path($employee->profile_picture))) {
                @unlink(public_path($employee->profile_picture));
            }

            $employee->update([
                'profile_picture' => 'uploads/avatars/' . $filename,
            ]);

            $module = in_array($employee->status, ['resign', 'retired', 'transfer', 'others']) ? 'archive' : 'masterlist';
            \App\Models\ActivityLog::log('edit', $module, 'Updated profile picture for employee ' . $employee->name);
        }

        return redirect()->route('employees.show', ['id' => $employee->id, 'tab' => 'personal'])
            ->with('success_message', 'Profile picture updated successfully.');
    }
}
