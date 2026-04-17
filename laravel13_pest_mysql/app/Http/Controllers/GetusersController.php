<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class GetusersController extends Controller
{
    /**
     * Get all users.
     */
    #[OA\Get(
        path: '/api/getallusers',
        tags: ['Users'],
        summary: 'Get all users',
        security: [['sanctum' => []]],
    )]
    #[OA\Response(response: 200, description: 'User Authenticated Successfully')]
    #[OA\Response(response: 401, description: 'Un-Authorized Access')]
    #[OA\Response(response: 404, description: 'Users is empty')]
    public function getAllusers() {
        if (Auth::guard('sanctum')->check()) {
            $users = User::all();
            if ($users->count() == 0) {
                return response()->json(['message' => 'Users is empty.'],404);
            }
            return response()->json(['message' => 'User Authenticated Successfully.', 'user' => $users],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }
    
}
