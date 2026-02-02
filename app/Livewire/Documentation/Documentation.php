<?php

namespace App\Livewire\Documentation;

use App\Models\Detail;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;
use function Spatie\LaravelPdf\Support\pdf;

class
Documentation extends Component
{
    public $documentation;
    public array $selectedDownloads = [];
    public $locale;

    public function render()
    {
        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->where('lang','nl')->get();
        } elseif ($this->locale === 'en') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->where('lang','en')->get();
        }


        return view('livewire.documentation.documentation');
    }

    public function uploadDocumentation() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/documentation/upload', navigate: true);
        }
        else {
            return $this->redirect('/documentation', navigate: true);
        }
    }

    public function updateDownload() {
        $this->selectedDownloads = $this->selectedDownloads;
    }

    public function downloadSelected()
    {
        if (empty($this->selectedDownloads)) {
            session()->flash('error','Geen bestand geselecteerd.');
            return;
        }

        $params = [
            'files' => $this->selectedDownloads,
            'route' => 'documentation',
        ];

        $query = http_build_query($params);
        $url = route('download.bulk.zip') . '?' . $query;

        // Livewire 3 event naar frontend
        $this->dispatch('download-zip', url: $url);
    }

    public function downloadAll() {

        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->documentation as $documentation) {

                // pad in storage/app/public
                $filePath = public_path('/storage/documentation/' . $documentation->file_name);

                $filePath = str_replace(['%20'], ' ', $filePath);

                if (file_exists($filePath)) {
                    $zip->addFileFromPath(
                        basename($filePath), // naam in zip
                        $filePath
                    );
                }
            }

            $zip->finish();

        }, 'documentatie.zip');
    }
}
