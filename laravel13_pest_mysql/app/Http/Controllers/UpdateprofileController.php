<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateprofileController extends Controller
{
    public function updateUser(string $id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found...'],404);
            }
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mobile = $request->mobile;
            $user->save();
            return response()->json(['message' => 'Profile updated sucessfully...'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
    
}
