<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    /**
     * Display the audit trail list.
     */
    public function index()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('admin.audit-trail', compact('logs'));
    }

    /**
     * Filter logs via AJAX.
     */
    public function filter(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'LIKE', "%$search%")
                  ->orWhere('module', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%$search%");
                  });
            });
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month') && $request->month !== 'all') {
            $query->whereMonth('created_at', $request->month);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('partials.audit-trail-table', compact('logs'))->render();
    }

    /**
     * Delete logs for a specific month and year.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        $count = ActivityLog::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->delete();

        ActivityLog::log('delete', 'audit_trail', "Deleted $count audit logs from $start to $end");

        return back()->with('success', "Successfully deleted $count logs for the period $start to $end.");
    }
}
