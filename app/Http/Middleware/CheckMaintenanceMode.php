<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Maintenance;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        $maintenance = Maintenance::first();
        
        if ($maintenance && $maintenance->is_maintenance) {
            if (!auth()->check() || auth()->user()->role->name !== 'System Admin') {
                if ($request->ajax()) {
                    return response()->json(['error' => 'System is under maintenance'], 503);
                }
                return redirect()->route('login')->with('maintenance_mode', true);
            }
        }

        return $next($request);
    }
}
