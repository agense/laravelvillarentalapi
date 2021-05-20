<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthenticationController extends Controller
{
    /**
     * @param Request $request
     */
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:150|regex:/(^[A-Za-z0-9 ]+$)+/',
            'email' => 'required|max:191|email|unique:users,email',
            'password' => 'required|string|min:6|max:20|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => new UserResource($user),'token' => $user->getAccessToken()], 201);
    }

    /**
     * @param Request $request
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

        return response()->json(['user' => new UserResource($user),'token' => $user->getAccessToken()], 200);
    }

    /**
     * Delete all users access tokens
     */
    public function logout(){
        auth()->user()->tokens()->delete();
        return response(['message' => 'Logout successful'], 200);
    }
}
