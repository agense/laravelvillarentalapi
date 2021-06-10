<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset link to the given user via email
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        Password::sendResetLink(
            $request->only('email')
        );
        if(Password::RESET_LINK_SENT){
            return response()->json([
                'message' => 'Password Reset Email Sent',
                'password_reset_via_api' => [
                    'url' => route('password.reset'),
                    'method' => 'POST',
                    'arguments' => [
                        'token' => 'password reset link token received via email notification',
                        'email' => 'users email',
                        'password' => 'new password',
                        'password_confirmation' => 'new password confirmed'
                    ]
                ]
            ], 200);
        }else{
            return response()->json(['message' => 'Failed to send password reset email'], 500);
        }   
    }
}
