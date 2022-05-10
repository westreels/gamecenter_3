<?php

namespace Packages\HeadsOrTails\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function uploadImage(Request $request)
    {
        $path = FALSE;

        // check if image file is passed
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // save uploaded image
            $path = $request->image->storeAs('games/heads-or-tails', $request->image->getClientOriginalName(), 'public');
        }

        return $path
            ? $this->successResponse(['path' => '/storage/' . $path])
            : $this->errorResponse(__('There was an error while uploading the image.'));
    }
}
