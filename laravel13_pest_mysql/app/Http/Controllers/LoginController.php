<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
// uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Arr;

class LoginController extends Controller
{
 public function login(Request $request)
 {
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
        $user = Auth::user();
        
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;
        $rolename = $user->roles->pluck('name')->first();

        return response()->json([
            'message' => 'Login successful.',
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'username' => $user->username,
            'isactivated' => $user->isactivated,
            'isblocked' => $user->isblocked,
            'mailtoken' => $user->mailtoken,
            'roles' => $rolename,
            'profilepic' => $user->profilepic,
            'qrcodeurl' => $user->qrcodeurl,
            'token' => $token
        ], 200);
    }

    return response()->json(['message' => 'Invalid credentials.'], 401);
 }
}
