<?php

namespace App\Livewire\Details;

use App\Models\Detail;
use App\Models\Order;
use App\Models\Regulation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use STS\ZipStream\Facades\Zip;
use ZipArchive;
use ZipStream\ZipStream;
use function Spatie\LaravelPdf\Support\pdf;

class
Details extends Component
{
    public $details;
    public array $selectedDownloads = [];
    public array $allDownloads = [];

    public function render()
    {

        $this->details = Detail::orderBy('order_id', 'asc')->get();


        foreach($this->details as $detail) {
            array_push($this->allDownloads,$detail->file_name);
        }

        return view('livewire.details.details');
    }

    public function uploadDetails() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/details/upload', navigate: true);
        }
        else {
            return $this->redirect('/details', navigate: true);
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
            'route' => 'details',
        ];


        $query = http_build_query($params);
        $url = route('download.bulk.zip') . '?' . $query;

        // Livewire 3 event naar frontend
        $this->dispatch('download-zip', url: $url);
    }

    public function downloadAll() {

        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->details as $detail) {

                // pad in storage/app/public
                $filePath = public_path('/storage/details/' . $detail->file_name);

                $filePath = str_replace(['%20'], ' ', $filePath);

                if (file_exists($filePath)) {
                    $zip->addFileFromPath(
                        basename($filePath), // naam in zip
                        $filePath
                    );
                }
            }

            $zip->finish();

        }, 'details.zip');
    }
}
