<?php

namespace App\Http\Controllers\Api;


use App\Handlers\FileuploadHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function uploadAvatar(Request $request, FileuploadHandler $fileuploadHandler)
    {
        $file = $request->file('avatar');

        $rest_upload_image = $fileuploadHandler->uploadImage($file, Auth::id(), 300, 'avatars');
        if ($rest_upload_image['status'] === true) {
            return $this->success($rest_upload_image['data']);
        } else {
            return $this->failed($rest_upload_image['message']);
        }
    }

    public function tinymceUpload(Request $request, FileuploadHandler $fileuploadHandler)
    {
        $file = $request->file('tinymce');

        $rest_upload_image = $fileuploadHandler->uploadImage($file, Auth::id(), 800, 'editors');
        if ($rest_upload_image['status'] === true) {
            return $this->success($rest_upload_image['data']);
        } else {
            return $this->failed($rest_upload_image['message']);
        }

    }

    public function advertisementUpload(Request $request, FileuploadHandler $fileuploadHandler)
    {
        $file = $request->file('advertisement');

        $rest_upload_image = $fileuploadHandler->uploadImage($file, Auth::id(), 800, 'advertisements');
        if ($rest_upload_image['status'] === true) {
            return $this->success($rest_upload_image['data']);
        } else {
            return $this->failed($rest_upload_image['message']);
        }

    }

    public function otherUpload(Request $request, FileuploadHandler $fileuploadHandler)
    {
        $file = $request->file('other');

        $rest_upload_image = $fileuploadHandler->uploadImage($file, Auth::id(), $request->post('max_width'), 'others');
        if ($rest_upload_image['status'] === true) {
            return $this->success($rest_upload_image['data']);
        } else {
            return $this->failed($rest_upload_image['message']);
        }

    }
}
