<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelpController extends Controller
{
    public function index(Request $request)
    {
        $files = collect(Storage::disk('assets')->files('help'))
            ->map(function ($path) {
                return str_replace('.html', '', basename($path));
            });

        $file = 'help/' . $request->file . '.html';
        $content = Storage::disk('assets')->exists($file) ? Storage::disk('assets')->get($file) : '';

        return compact('files', 'content');
    }
}
