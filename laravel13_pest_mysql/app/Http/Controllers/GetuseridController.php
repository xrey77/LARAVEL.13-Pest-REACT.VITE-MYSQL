<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetuseridController extends Controller
{
    public function getUserbydid(string $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            return response()->json(['message' => 'User Authenticated Successfully.','user' => $user], 200);

        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
}
