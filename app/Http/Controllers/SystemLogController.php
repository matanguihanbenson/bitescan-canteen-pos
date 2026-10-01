<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::with('user')
            ->accessibleByRole(Auth::user())
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('username', 'like', "%{$searchTerm}%")
                               ->orWhere('firstname', 'like', "%{$searchTerm}%")
                               ->orWhere('lastname', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $logs = $query->paginate(50);

        $modules = SystemLog::accessibleByRole(Auth::user())
            ->distinct()
            ->pluck('module')
            ->sort();

        $actions = SystemLog::accessibleByRole(Auth::user())
            ->distinct()
            ->pluck('action')
            ->sort();

        $users = \App\Models\User::select('id', 'username', 'firstname', 'lastname')
            ->orderBy('username')
            ->get();

        if (!Auth::user()->hasPermission('view_system_logs')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to view system logs.');
        }

        return view('system-logs.index', compact('logs', 'modules', 'actions', 'users'));
    }

    public function show($id)
    {
        $log = SystemLog::with('user')
            ->accessibleByRole(Auth::user())
            ->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'log' => [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->full_name : 'System',
                    'username' => $log->user ? $log->user->username : 'N/A',
                    'action' => $log->action,
                    'module' => $log->module,
                    'description' => $log->description,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'severity' => $log->severity,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $log->created_at->diffForHumans()
                ]
            ]);
        }

        return view('system-logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $query = SystemLog::with('user')
            ->accessibleByRole(Auth::user())
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(1000)->get();

        $csv = fopen('php://temp', 'w');

        fputcsv($csv, [
            'ID',
            'Date/Time',
            'User',
            'Action',
            'Module',
            'Description',
            'IP Address',
            'Severity'
        ]);

        foreach ($logs as $log) {
            fputcsv($csv, [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->full_name : 'System',
                $log->action,
                $log->module,
                $log->description,
                $log->ip_address,
                $log->severity
            ]);
        }

        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        \App\Services\ActivityLogger::exportData('System Logs', 'CSV');

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="system-logs-' . date('Y-m-d-His') . '.csv"');
    }

    public function stats()
    {
        $user = Auth::user();
        
        $stats = [
            'today' => SystemLog::accessibleByRole($user)->today()->count(),
            'this_week' => SystemLog::accessibleByRole($user)->where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => SystemLog::accessibleByRole($user)->where('created_at', '>=', now()->startOfMonth())->count(),
            'total' => SystemLog::accessibleByRole($user)->count(),
            'critical' => SystemLog::accessibleByRole($user)->bySeverity('critical')->count(),
            'errors' => SystemLog::accessibleByRole($user)->bySeverity('error')->count(),
        ];

        return response()->json($stats);
    }
}