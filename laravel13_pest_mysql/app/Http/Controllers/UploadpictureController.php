<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Image;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class UploadpictureController extends Controller
{
    #[OA\Patch(
        path: '/api/uploadpicture/{id}',
        tags: ['Users'],                
        summary: 'Update profile picture'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['id', 'profilepic'],
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'profilepic', type: 'string', format: 'binary')
                ]
            )
        )
    )]
    #[OA\Response(response: 200, description: 'Picture uploaded')]
    #[OA\Response(response: 404, description: 'User or Image not found')]
    public function updateProfilepicture(Request $request, KafkaProducerService $kafkaService) 
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

            $data = [
                'event' => 'upload_picture',
                'user_id' => $user->id
            ];

            $kafkaService->publishMessage('central-topic', $data, $user);

            return response()->json(['message' => 'New picture has been uploaded successfully.'],200);
        } else {
            return response()->json(['message' => 'Image not found.'],404);
        }

    }    
}
