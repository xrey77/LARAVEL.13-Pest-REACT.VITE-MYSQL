<?php

namespace App\Http\Controllers;

use Symfony\Component\Console\Output\ConsoleOutput;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA; 

use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class MfavalidationController extends Controller
{
    #[OA\Patch(
        path: '/api/mfa/verifytotp/{id}',
        tags: ["Authentication"],
        summary: 'Validate OTP code'
        )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['id', 'otp'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'otp', type: 'string', example: '123456')
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'OTP Validated')]     
    public function validateOtp(Request $request, KafkaProducerService $kafkaService, ?int $id) {
        $user = User::find($id);
        $otp = $request->input('otp');

        if ($user) {
            if (empty($user->secretkey)) {
                return response()->json(['message' => '2FA secret not found for this user.'], 400);
            }

            try {
                $secret = decrypt($user->secretkey);
                $google2fa = new Google2FA();       
                if ($google2fa->verifyKey($secret, $otp, 1)) { 
                    $data = [
                        'event' => 'mfa_validation',
                        'user_id' => $user->id
                    ];

                    $kafkaService->publishMessage('central-topic', $data, $user);

                    return response()->json(['message' => 'OTP Code is successfully validated.', 'username' => $user->username], 200);
                } else {
                    return response()->json(['message' => 'Invalid OTP code, please try again.'], 404);
                }
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['message' => 'Encryption mismatch or invalid secret payload.'], 400);
            } catch (\Exception $e) {
                return response()->json(['message' => 'An unexpected error occurred.'], 500);
            }
        }

        return response()->json(['message' => 'User not found.'], 404);
    }
}
