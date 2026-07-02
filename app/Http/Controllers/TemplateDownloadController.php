<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateDownloadController extends Controller
{
    public function download(Request $request, string $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $templates = config('eteeap.templates', []);

        abort_unless(isset($templates[$type]), 404, "Template '{$type}' not found.");

        $filename = $templates[$type];
        $path = 'templates/' . $filename;

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, $filename);
    }
}
