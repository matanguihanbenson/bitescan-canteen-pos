<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::user()->hasPermission($permission)) {
            session()->flash('return_url', url()->previous());
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
