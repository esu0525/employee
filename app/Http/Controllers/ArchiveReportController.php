<?php

namespace App\Http\Controllers;

use App\Models\ArchiveReport;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class ArchiveReportController extends Controller
{
    /**
     * Store a new report record.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'period_coverage' => 'nullable|string|max:255',
            'regional_office' => 'nullable|string|max:255',
            'file_name' => 'required|string|max:255',
            'format' => 'required|string|in:pdf,excel',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'string',
        ]);

        $user = User::find(session('auth_user_id'));

        $report = ArchiveReport::create([
            'title' => $data['title'],
            'period_coverage' => $data['period_coverage'] ?? null,
            'regional_office' => $data['regional_office'] ?? null,
            'file_name' => $data['file_name'],
            'format' => $data['format'],
            'employee_ids' => $data['employee_ids'],
            'employee_count' => count($data['employee_ids']),
            'generated_by' => $user ? $user->name : 'System',
        ]);

        \App\Models\ActivityLog::log('create', 'archive', 'Generated archive report: ' . $data['title'] . ' (' . count($data['employee_ids']) . ' employees)');

        return response()->json([
            'success' => true,
            'report' => $report,
            'message' => 'Report saved successfully.',
        ]);
    }

    /**
     * Get all reports as JSON.
     */
    public function index(Request $request)
    {
        $reports = ArchiveReport::orderBy('created_at', 'desc')->get();
        return response()->json($reports);
    }

    /**
     * Delete a report.
     */
    public function destroy($id)
    {
        $report = ArchiveReport::findOrFail($id);
        $title = $report->title;
        $report->delete();

        \App\Models\ActivityLog::log('delete', 'archive', 'Deleted archive report: ' . $title);

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully.'
        ]);
    }

    public function reportedEmployeeIds()
    {
        $allIds = ArchiveReport::pluck('employee_ids')->flatten()->unique()->values();
        return response()->json($allIds);
    }

    /**
     * Download the report as a CSV/Excel file.
     */
    public function download($id)
    {
        $report = ArchiveReport::findOrFail($id);
        $employeeIds = $report->employee_ids;
        $employees = Employee::whereIn('id', $employeeIds)->orderBy('name', 'asc')->get();

        $filename = $report->file_name . ($report->format === 'pdf' ? '.pdf' : '.csv');
        
        // Header for CSV
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['#', 'AGENCY', 'NAME', 'POSITION', 'SG', 'LEVEL', 'STATUS', 'EFFECTIVITY', 'MODE'];

        $callback = function() use($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($employees as $index => $emp) {
                $effDate = $emp->effective_date ? \Carbon\Carbon::parse($emp->effective_date)->format('Y-m-d') : '-';
                fputcsv($file, [
                    $index + 1,
                    strtoupper($emp->agency ?: $emp->school ?: '-'),
                    strtoupper($emp->name),
                    strtoupper($emp->position ?: '-'),
                    $emp->salary_grade ?: '-',
                    strtoupper($emp->level_of_position ?: '-'),
                    strtoupper($emp->employment_status ?: '-'),
                    $effDate,
                    strtoupper($emp->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
