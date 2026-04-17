<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class UpdateprofileController extends Controller
{
    #[OA\Patch(
        path: '/api/profileupdate/{id}',
        tags: ['Users'],
        summary: 'Update user details',
        security: [['sanctum' => []]]
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string'),
                new OA\Property(property: 'mobile', type: 'string'),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Profile updated successfully')]
    #[OA\Response(response: 401, description: 'Un-Authorized Access')]
    #[OA\Response(response: 404, description: 'User not found')]
    public function updateUser(string $id, Request $request, KafkaProducerService $kafkaService) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found...'],404);
            }
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mobile = $request->mobile;
            $user->save();

            $data = [
                'event' => 'update_profile',
                'user_id' => $user->id
            ];

            $kafkaService->publishMessage('central-topic', $data, $user);

            return response()->json(['message' => 'Profile updated sucessfully...'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
    
}
