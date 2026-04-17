<?php

namespace App\Http\Controllers;

use Symfony\Component\Console\Output\ConsoleOutput;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA; 
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

use Exception;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class ActivatemfaController extends Controller
{
    #[OA\Patch(
        path: '/api/mfa/activate/{id}',
        tags: ["Authentication"],
        summary: 'Enable/Disable MFA',
        security: [['sanctum' => []]], 
        )]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [new OA\Property(property: 'Twofactorenabled', type: 'boolean', example: true)]))]
    #[OA\Response(
        response: 200, 
        description: 'MFA Status Updated',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'message', type: 'string'),
            new OA\Property(property: 'qrcodeurl', type: 'string', nullable: true)
        ])
    )]
    public function enableMfa(Request $request, KafkaProducerService $kafkaService, ?int $id) {
        if (Auth::guard('sanctum')->check()) {

            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found...'],404);
            }

            if ($request->Twofactorenabled) {
                $issuer = config('services.issuer_service.key');
                $google2fa = new Google2FA();
                $secretKey = $google2fa->generateSecretKey();
                $qrCodeUrl = $google2fa->getQRCodeUrl(
                    $issuer,
                    $user->email,
                    $secretKey
                );

                $renderer = new ImageRenderer(
                    new RendererStyle(200),
                    new ImagickImageBackEnd()
                );
                $writer = new Writer($renderer);
                
                $qrcode_image_string = $writer->writeString($qrCodeUrl);        
                $qrcode_base64 = base64_encode($qrcode_image_string);
                $qrcode = 'data:image/png;base64,' . $qrcode_base64;

                // $out = new ConsoleOutput();
                // $out->writeln($qrcode);

                // $user->google2fa_secret = encrypt($secretKey);
                $user->secretkey = encrypt($secretKey);
                $user->qrcodeurl = $qrcode;
                $user->save();
                $data = [
                    'event' => 'activate_mfa',
                    'user_id' => $user->id
                ];

                $kafkaService->publishMessage('central-topic', $data, $user);
                return response()->json(['message' => 'Multi-Factor Authenticator has been Enabled successfully, please scan QRCODE using your Google Authenticator from your Mobile Phone!', 'qrcodeurl' => $qrcode],200);
            } else {
                $user->secretkey = null;
                $user->qrcodeurl = null;
                $user->save();
                return response()->json(['message' => 'Multi-Factor Authenticator has been Disabled successfully.', 'qrcodeurl' => null],200);
            }

        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }
    }    
}
