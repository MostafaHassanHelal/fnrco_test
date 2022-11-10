<?php

namespace App\Http\Controllers;

use App\Concerns\HandlesFiles;
use App\Http\Requests\UploadImageRequest;

class ImageController extends Controller
{
    use HandlesFiles;

    public function uploadImage(UploadImageRequest $request)
    {
        $url = $request->image->store("images", "public");
        $url = $this->getDefaultUrl() . $url;
        return response()->json(['status' => 'success', 'url' => $url]);
    }
}
