<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response(['message' => 'Incorrect Credentials'], 401);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->getAccessToken()
        ], 200);
    }

    /**
     * Delete all users access tokens
     * @return \Illuminate\Http\Response
     */
    public function logout(){
        auth()->user()->tokens()->delete();
        return response(['message' => 'Logout successful'], 200);
    }
}
