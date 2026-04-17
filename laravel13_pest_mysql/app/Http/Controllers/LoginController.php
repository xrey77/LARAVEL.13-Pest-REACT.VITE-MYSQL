<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Arr;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    #[OA\Post(
        path: "/api/login",
        tags: ["Authentication"],
        summary: "User Login",
        description: "Authenticates user and returns a plain text token.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["username", "password"],
                properties: [
                    new OA\Property(property: "username", type: "string", example: "johndoe"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login successfull."),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "username", type: "string", example: "johndoe"),
                        new OA\Property(property: "roles", type: "string", example: "admin"),
                        new OA\Property(property: "token", type: "string", example: "1|abcdef123456...")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Invalid credentials or user not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Username not found, please register first.")
                    ]
                )
            )
        ]
    )]    
    public function loginuser(Request $request, KafkaProducerService $kafkaService)
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

            $data = [
                'event' => 'user_login',
                'user_id' => $user->id
            ];

            $kafkaService->publishMessage('central-topic', $data, $user);

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
