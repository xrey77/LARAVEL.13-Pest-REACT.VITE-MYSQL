<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google2FA;

class MfavalidationController extends Controller
{
   public function validateOtp(Request $request) {
            $user = User::find($request->id);
            if ($user) {
              try {
                $secret = decrypt($user->google2fa_secret);
                $otp = $request->otp;
                if (Google2FA::verifyKey($secret, $otp)) {
                    // Google2FA::login();
                    return response()->json(['message' => 'OTP Code is successfully validated.','username' => $user->username],200);
                } else {
                    return response()->json(['message' => 'Invalid OTP code, please try again.'], 404);
                }
              } catch(\Exception $e) {
                return response()->json(['message' => $e->getMessage()]);
              }
            } else {
                return response()->json(['message' => 'Un-Authorized access.'], 401);
            }
    }    
}
