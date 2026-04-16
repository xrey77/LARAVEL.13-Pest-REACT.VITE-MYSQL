<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{

    public function register(Request $request) 
    {

        $validated = $request->validate([
            'email' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $mobile = $request->mobile;
        $username = $request->username;
        $password = Hash::make($request->password);

        $emailUser = User::where('email', $email)->first();
        if ($emailUser) {
            return response()->json(['message' => 'Email Address is already taken.'],400);
        }

        $userName = User::where('username', $username)->first();
        if ($userName) {
            return response()->json(['message' => 'Username is already taken.'],400);
        }

        $roleId = 2;
        $isactivated = 1;
        $user = User::create ([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'mobile' => $mobile,
            'username' => $username,
            'password' => $password,
            'isactivated' => $isactivated,
            'role_id' => $roleId,
            'profilepic' => 'pix.png',
        ]);
        $user->roles()->attach($roleId);
        
        $secret = "";
        $user = User::where('username', $username)->first();
        $credentials = ['username' => $username, 'password' => $password];                
        if (Hash::check($request->password, $user->password)) {
            Auth::attempt($credentials);
            $secret = encrypt($user->two_factor_secret);        
            $user->two_factor_secret = $secret;
            $user->save();                    
        }
        
        return response()->json([
            'message' => 'User registered successfully.',
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email'=> $email,
            'mobile' => $mobile,
            'username' => $username,
            'password' => $password],201);
    }


}
