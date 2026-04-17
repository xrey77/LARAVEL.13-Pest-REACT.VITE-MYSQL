<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class DeleteuserController extends Controller
{
    #[OA\Delete(
        path: '/api/deleteuser/{id}',
        tags: ['Users'],
        summary: 'Delete a user',        
        security: [['sanctum' => []]],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'User Deleted successfully')]
    #[OA\Response(response: 401, description: 'Un-Authorized Access')]    
    public function deleteUser(int $id, KafkaProducerService $kafkaService) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::findOrFail($id);
            $user->delete();

            $data = [
                'event' => 'delete_user',
                'user_id' => $user->id
            ];

            $kafkaService->publishMessage('central-topic', $data, $user);

            return response()->json(['message' => 'User Deleted successfully.'],200);
        } else {
            return response()->json(['message' => 'Un-Authorized Access.'], 401);
        }
    }    
}
