<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Image;

class UploadpictureController extends Controller
{
    public function updateProfilepicture(Request $request) 
    {
        $userid = $request->id;
        $user = User::find($userid);
        if (!$user) {
            return response()->json(['message' => 'User not found...'],404);
        }
        // GET MULTIPART FORM FILE
        if ($request->hasFile('profilepic')) {
            $file = $request->file('profilepic');
            $img = $file->getClientOriginalName();
            // ASSIGN NEW FILENAME
            $ext = $request->file('profilepic')->guessExtension(); 
            $newfile = '00' . $userid . '.' . $ext;
            $img = Image::read($file->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('users/' . $newfile));
        
            // Store the original image
            $file->move(public_path('users'), $newfile);
            
            $user = User::find($userid);
            if($user) {
                $user->profilepic = "users/" . $newfile;
                $user->save();
            }    
            return response()->json(['message' => 'New picture has been uploaded successfully.'],200);
        } else {
            return response()->json(['message' => 'Image not found.'],404);
        }

    }    
}
