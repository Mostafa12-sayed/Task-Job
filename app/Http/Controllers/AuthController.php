<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $email = User::where('email', $request->email)->first();
        if ($email) {
            $credentials = $request->only('email', 'password');
            if (auth()->attempt($credentials)) {
                $user = auth()->user();
                $token = $request->user()->createToken('auth_token')->plainTextToken;
                return response(['user' => $user->id, 'token' => $token]);
            } else {
                return response(['password' => 'password is incorrect'], 401);
            }
        }
        return response(['email' => 'email is incorrect'], 401);
    }

    public function logout(Request $request)
    {


        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
        $token = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'token' => $token], 201);
    }

    public function getUser(Request $request)
    {
        return auth()->user();
    }
}
