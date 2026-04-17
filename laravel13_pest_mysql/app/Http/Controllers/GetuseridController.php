<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class GetuseridController extends Controller
{
    #[OA\Get(
        path: '/api/getuserid/{id}',
        tags: ['Users'],
        summary: 'Retrieve by user id',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the user to retrieve',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'User Authenticated Successfully'),
            new OA\Response(response: 401, description: 'Un-Authorized Access')
        ]
    )]      
    public function getUserbydid(string $id, KafkaProducerService $kafkaService) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);

            $data = [
                'event' => 'getuser_id',
                'user_id' => $user->id
            ];

            $kafkaService->publishMessage('central-topic', $data, $user);

            return response()->json(['message' => 'User Authenticated Successfully.','user' => $user], 200);

        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
}
