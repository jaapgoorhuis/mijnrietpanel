<?php

namespace App\Livewire\Pricelist;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;
use function Spatie\LaravelPdf\Support\pdf;
use STS\ZipStream\Facades\Zip;

class
Pricelist extends Component
{
    public $pricelist;

    public array $selectedDownloads = [];

    public $locale;



    public function mount() {
        if(Auth::user()->is_admin || !Auth::user()->is_architect) {
            return view('livewire.pricelist.pricelist');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function render()
    {

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->pricelist = \App\Models\Pricelist::orderBy('order_id', 'asc')->where('lang','nl')->get();
        } elseif ($this->locale === 'en') {
            $this->pricelist = \App\Models\Pricelist::orderBy('order_id', 'asc')->where('lang','en')->get();
        }
        return view('livewire.pricelist.pricelist');
    }


    public function updateDownload() {
        $this->selectedDownloads = $this->selectedDownloads;
    }
    public function uploadPricelist() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/pricelist/upload', navigate: true);
        }
        else {
            return $this->redirect('/pricelist', navigate: true);
        }
    }

    public function downloadSelected()
    {
        if (empty($this->selectedDownloads)) {
            session()->flash('error','Geen bestand geselecteerd.');
            return;
        }

        $params = [
            'files' => $this->selectedDownloads,
            'route' => 'pricelist',
        ];

        $query = http_build_query($params);
        $url = route('download.bulk.zip') . '?' . $query;

        // Livewire 3 event naar frontend
        $this->dispatch('download-zip', url: $url);
    }

    public function downloadAll() {

        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->pricelist as $pricelist) {

                // pad in storage/app/public
                $filePath = public_path('/storage/pricelist/' . $pricelist->file_name);

                $filePath = str_replace(['%20'], ' ', $filePath);

                if (file_exists($filePath)) {
                    $zip->addFileFromPath(
                        basename($filePath), // naam in zip
                        $filePath
                    );
                }
            }

            $zip->finish();

        }, 'prijslijst.zip');
    }


}
