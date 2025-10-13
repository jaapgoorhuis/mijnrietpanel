<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use STS\ZipStream\Facades\Zip;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ZipDownloadController extends Controller
{
    public function download(Request $request)
    {
        $files = $request->query('files', []);

        $fileRoute = $request->query('route');

        if (empty($files)) {
            abort(400, 'Geen bestanden opgegeven');
        }

        $zip = Zip::create('archive.zip');

        foreach ($files as $fileName) {
            $zip->add(storage_path('app/public/'.$fileRoute.'/'.$fileName));
        }

        return $zip;
    }
}
