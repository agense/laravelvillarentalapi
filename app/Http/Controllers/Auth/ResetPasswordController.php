<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    /**
     * Reset the given user's password.
     * @param  \App\Http\Requests\ResetPasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->updatePassword($password);
            }
        );
        if(Password::PASSWORD_RESET){
            return response()->json(['message' => 'Password Reset Successfull'], 200);
        }else{
            return response()->json(['message'=> 'Password Reset Failed'], 500);
        }
    }

}
