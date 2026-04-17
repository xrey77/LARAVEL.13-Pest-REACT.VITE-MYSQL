<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class ChangepasswordController extends Controller
{
    #[OA\Patch(
        path: '/api/changepassword/{id}',
        tags: ['Users'],
        summary: 'Change user password',
        security: [['sanctum' => []]],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['password'],
            properties: [
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'newpassword123')
            ]
        )
    )]
    #[OA\Response(
        response: 200, 
        description: 'Success', 
        content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string')])
    )]
    public function changeUserpassword(Request $request, KafkaProducerService $kafkaService, int $id)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            $user->password = Hash::make($request->password);
            $user->save();

            $data = [
                'event' => 'change_password',
                'user_id' => $user->id
            ];

            $kafkaService->publishMessage('central-topic', $data, $user);

            return response()->json(['message' => 'You change your password successfully....'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
}
