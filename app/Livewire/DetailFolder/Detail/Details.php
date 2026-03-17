<?php

namespace App\Livewire\DetailFolder\Detail;

use App\Models\Detail;
use App\Models\DetailFolder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;

class
Details extends Component
{
    public $details;
    public array $selectedDownloads = [];
    public array $allDownloads = [];

    public $locale;
    public $folderId;
    public $folder;

    public function mount($id) {

        $this->folder = DetailFolder::find($id);
        $this->folderId = $id;

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->details = Detail::orderBy('order_id', 'asc')->where('lang', 'nl')->where('detailsFolder_id', $id)->get();
        } elseif ($this->locale === 'en') {
            $this->details = Detail::orderBy('order_id', 'asc')->where('lang','en')->where('detailsFolder_id', $id)->get();
        }
    }
    public function render()
    {
        foreach($this->details as $detail) {
            array_push($this->allDownloads,$detail->file_name);
        }

        return view('livewire.detailFolders.details.details');
    }

    public function uploadDetails() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/detail-maps/'.$this->folderId.'/details/upload', navigate: true);
        }
        else {
            return $this->redirect('/detail-maps', navigate: true);
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

        }, $this->folder->name.'.zip');
    }
}
