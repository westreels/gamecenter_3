<?php

namespace Packages\Slots\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function cloneVariation(Request $request, $id)
    {
        $sourcePath = storage_path('app/public/games/slots/' . $id);
        $destinationPath = storage_path('app/public/games/slots/' . $request->id);

        // it's not necessary to copy symbol images when cloning default system games
        if (File::exists($sourcePath)) {
            return File::copyDirectory($sourcePath, $destinationPath)
                ? $this->successResponse()
                : $this->errorResponse(__('There was an error while copying symbol images.'));
        }

        return $this->successResponse();
    }

    public function removeVariation(Request $request, $id)
    {
        $sourcePath = storage_path('app/public/games/slots/' . $id);

        // default system games files located in public/images/slots do not need to be deleted
        if (File::exists($sourcePath)) {
            return File::deleteDirectory(storage_path('app/public/games/slots/' . $id))
                ? $this->successResponse()
                : $this->errorResponse(__('There was an error while deleting symbol images.'));
        }

        return $this->successResponse();
    }
}
