<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
use STS\ZipStream\Facades\Zip;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ZipDownloadController extends Controller
{
    public function download(Request $request)
    {
        $files = $request->query('files', []);

        $fileRoute = $request->query('route');
        $name = $request->query('name');

        if (empty($files)) {
            abort(400, 'Geen bestanden opgegeven');
        }

        $filename = Str::slug($name) . '.zip';

        $zip = Zip::create($filename);

        foreach ($files as $fileName) {
            $zip->add(public_path('storage/'.$fileRoute.'/'.$fileName));
        }

        return $zip;
    }
}
