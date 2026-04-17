<?php

namespace App\Http\Controllers;

use Symfony\Component\Console\Output\ConsoleOutput;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Google2FA;
// use PragmaRX\Google2FA\Google2FA; 

// use App\Services\KafkaProducerService;
// use OpenApi\Attributes as OA;

class MfavalidationController extends Controller
{
    // #[OA\Post(
    //     path: '/api/mfa/verifytotp/{id}',
    //     tags: ["Users"],
    //     summary: 'Validate OTP code'
    //     )]
    // #[OA\RequestBody(
    //     required: true,
    //     content: new OA\JsonContent(
    //         required: ['id', 'otp'],
    //         properties: [
    //             new OA\Property(property: 'id', type: 'integer', example: 1),
    //             new OA\Property(property: 'otp', type: 'string', example: '123456')
    //         ]
    //     )
    // )]
    // #[OA\Response(response: 200, description: 'OTP Validated')]
    //  KafkaProducerService $kafkaService,
public function validateOtp(Request $request, ?int $id) {
    $user = User::find($id);
    $otp = $request->input('otp'); // Ensure $otp is defined from request

    if ($user) {
        // 1. Check if secretkey exists and is not empty before decrypting
        if (empty($user->secretkey)) {
            return response()->json(['message' => '2FA secret not found for this user.'], 200);
        }

        try {
            $secret = decrypt($user->secretkey);
            
            if (Google2FA::verifyKey($secret, $otp, 1)) { 
                return response()->json(['message' => 'OTP Code is successfully validated.', 'username' => $user->username], 200);
            } else {
                // Keep 200 if you want to avoid exposing internal logic/errors to the client
                return response()->json(['message' => 'Invalid OTP code, please try again.'], 200);
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // 2. Catch specific decryption failures and return a 200 status
            return response()->json(['message' => 'Encryption mismatch or invalid secret payload.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 200);
        }
    }

    return response()->json(['message' => 'User not found.'], 200);
}

//    public function validateOtp(Request $request, KafkaProducerService $kafkaService, ?int $id) {
//             $user = User::find($id);
//             if ($user) {
//               try {
//                 $secret = decrypt($user->secretkey);
//                 $otp = $request->otp;
//                 if (Google2FA::verifyKey($secret, $otp)) {

//                     $data = [
//                         'event' => 'mfa_validation',
//                         'user_id' => $user->id
//                     ];

//                     $kafkaService->publishMessage('central-topic', $data, $user);

//                     return response()->json(['message' => 'OTP Code is successfully validated.','username' => $user->username],200);
//                 } else {
//                     return response()->json(['message' => 'Invalid OTP code, please try again.'], 404);
//                 }
//               } catch(\Exception $e) {
//                 return response()->json(['message' => $e->getMessage()]);
//               }
//             } else {
//                 return response()->json(['message' => 'Un-Authorized access.'], 401);
//             }
//     }    
}
