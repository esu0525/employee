<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
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

        return view('employees.index', compact(
            'employees', 
            'search', 
            'total_active', 
            'total_departments', 
            'filtered_count'
        ));
    }
}
