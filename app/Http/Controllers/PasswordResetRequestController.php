<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; 

class PasswordResetRequestController extends Controller
{
    public function requestReset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Email address not found in our records.'
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not found.'
                ], 404);
            }

            $existingRequest = PasswordResetRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'warning' => 'You already have a pending password reset request. Please wait for admin approval.'
                ], 200);
            }

            try {
                $resetRequest = PasswordResetRequest::create([
                    'user_id' => $user->id,
                    'status' => 'pending'
                ]);

                Log::info('Password reset request created:', ['user_id' => $user->id, 'request_id' => $resetRequest->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Your password reset request has been submitted successfully. Please wait for admin approval.'
                ], 200);

            } catch (\Exception $e) {
                Log::error('Failed to create password reset request:', ['error' => $e->getMessage()]);

                return response()->json([
                    'error' => 'Failed to create password reset request. Please try again later.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Password reset request error:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    public function adminIndex()
    {
        $requests = PasswordResetRequest::with('user')
            ->where('status', 'pending')
            ->get();
        
        return view('admin.password-requests.index', compact('requests'));
    }

    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $resetRequest = PasswordResetRequest::findOrFail($id);
        $user = $resetRequest->user;

        $user->password = Hash::make($request->new_password);
        $user->save();

        $resetRequest->update([
            'status' => 'approved',
            'admin_notes' => 'Password reset by admin'
        ]);

        return back()->with('success', 'Password has been reset successfully.');
    }

    public function rejectRequest($id)
    {
        try {
            $resetRequest = PasswordResetRequest::findOrFail($id);
            $resetRequest->status = 'rejected';
            $resetRequest->admin_notes = 'Request rejected by admin';
            $resetRequest->save();
            
            return redirect()->back()->with('success', 'Password reset request has been rejected.');
        } catch (\Exception $e) {
            Log::error('Failed to reject request:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to reject the request.');
        }
    }
}
