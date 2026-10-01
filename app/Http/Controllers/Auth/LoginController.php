<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\LoginLog;
use App\Services\SystemLogService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $redirectTo = '/home';
    protected $logService;

    public function __construct(SystemLogService $logService)
    {
        $this->logService = $logService;
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required', 
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $login_type => $request->input('login'),
            'password' => $request->input('password')
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            $maintenance = \App\Models\Maintenance::first();
            if ($maintenance && $maintenance->is_maintenance && $user->role->name !== 'System Admin') {
                Auth::logout();
                return back()->with('maintenance_mode', true)->withInput();
            }

            if ($user->status === 'deactivated') {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Your account has been deactivated. Please contact the system administrator.',
                ])->withInput();
            }

            // Update login tracking
            $user->update([
                'last_login' => now(),
                'login_count' => ($user->login_count ?? 0) + 1
            ]);

            // Log this login event
            LoginLog::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => now(),
            ]);

            // Log to system logs
            $this->logService->logAuth(
                'login',
                "User {$user->username} ({$user->firstname} {$user->lastname}) logged in successfully",
                'info'
            );

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout before logging out
        if ($user) {
            $this->logService->logAuth(
                'logout',
                "User {$user->username} ({$user->firstname} {$user->lastname}) logged out",
                'info'
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    
}
