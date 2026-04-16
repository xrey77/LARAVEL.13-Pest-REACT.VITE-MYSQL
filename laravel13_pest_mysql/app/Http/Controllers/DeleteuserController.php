<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteuserController extends Controller
{
    public function deleteUser(int $id) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User Deleted successfully.'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
}
