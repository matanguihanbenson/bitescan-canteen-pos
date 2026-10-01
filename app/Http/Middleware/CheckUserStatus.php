<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->status === 'deactivated') {
                Auth::logout();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your account has been deactivated. Please contact the system administrator.',
                        'deactivated' => true
                    ], 401);
                }
                
                return redirect()->route('login')
                    ->withErrors([
                        'login' => 'Your account has been deactivated. Please contact the system administrator.'
                    ]);
            }
        }
        
        return $next($request);
    }
}
