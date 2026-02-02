<?php

namespace App\Livewire\Marketing;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;
use function Spatie\LaravelPdf\Support\pdf;

class
Marketing extends Component
{
    public $marketing;
    public array $selectedDownloads = [];
    public $locale;
    public function render()
    {
        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->where('lang','nl')->get();
        } elseif ($this->locale === 'en') {
            $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        return view('livewire.marketing.marketing');
    }

    public function uploadMarketing() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/marketing/upload', navigate: true);
        }
        else {
            return $this->redirect('/marketing', navigate: true);
        }
    }

    public function updateDownload() {
        $this->selectedDownloads = $this->selectedDownloads;
    }


    public function downloadSelected()
    {
        if (empty($this->selectedDownloads)) {
            session()->flash('error',__('messages.Geen bestand geselecteerd.'));
            return;
        }

        $params = [
            'files' => $this->selectedDownloads,
            'route' => 'marketing',
        ];

        $query = http_build_query($params);
        $url = route('download.bulk.zip') . '?' . $query;

        // Livewire 3 event naar frontend
        $this->dispatch('download-zip', url: $url);
    }

    public function downloadAll() {

        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->marketing as $marketing) {

                // pad in storage/app/public
                $filePath = public_path('/storage/marketing/' . $marketing->file_name);

                $filePath = str_replace(['%20'], ' ', $filePath);

                if (file_exists($filePath)) {
                    $zip->addFileFromPath(
                        basename($filePath), // naam in zip
                        $filePath
                    );
                }
            }

            $zip->finish();

        }, 'marketing.zip');
    }
}
