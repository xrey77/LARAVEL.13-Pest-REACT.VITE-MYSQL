<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;
use Image;

class ActivatemfaController extends Controller
{
    public function enableMfa($id, Request $request) {
        if (Auth::guard('sanctum')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found...'],404);
            }
            $isEnabled = $request->Twofactorenabled;
            if ($isEnabled) {

                $issuer = config('services.issuer_service.key');
                $google2fa = new Google2FA();
                $secretKey = $google2fa->generateSecretKey();
                // Log::Debug("SECRET KEY :", encrypt($secretKey));
                $userEmail = $user->email;
                $companyName = $issuer;
                $qrCodeUrl = $google2fa->getQRCodeUrl(
                    $companyName,
                    $userEmail,
                    $secretKey
                );
        
                // Configure the PNG renderer for BaconQrCode
                $renderer = new ImageRenderer(
                    new RendererStyle(400), // Set the size
                    new ImagickImageBackEnd()
                );
                $writer = new Writer($renderer);
                
                // Write the QR code as a PNG image string
                $qrcode_image_string = $writer->writeString($qrCodeUrl);
        
                // Encode the image string to base64 for embedding in the view
                $qrcode_base64 = base64_encode($qrcode_image_string);

                $qrcode = 'data:image/svg+xml;base64,' . $qrcode_base64;
                $user->google2fa_secret = encrypt($secretKey);
                $user->qrcodeurl = $qrcode;
                $user->save();
                return response()->json(['message' => 'Multi-Factor Authenticator Enabled successfully, please scan QRCODE using your Google Authenticator from your Mobile Phone!', 'qrcodeurl' => $qrcode],200);
            } else {
                $user->qrcodeurl = null;
                $user->save();
                return response()->json(['message' => 'Multi-Factor Authenticator Disabled successfully.', 'qrcodeurl' => null],200);
            }

        } else {
            return response()->json(['message' => 'Un-Authorized access.'], 401);
        }
    }    
}
