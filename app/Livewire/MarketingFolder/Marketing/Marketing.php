<?php

namespace App\Livewire\MarketingFolder\Marketing;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use ZipStream\ZipStream;

class
Marketing extends Component
{
    public $marketing;
    public array $selectedDownloads = [];
    public array $allDownloads = [];

    public $locale;
    public $folderId;
    public $folder;

    public function mount($id) {

        $this->folder = \App\Models\MarketingFolder::find($id);
        $this->folderId = $id;

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->where('lang', 'nl')->where('marketing_category_id', $id)->get();
        } elseif ($this->locale === 'en') {
            $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->where('lang','en')->where('marketing_category_id', $id)->get();
        }
    }
    public function render()
    {
        foreach($this->marketing as $marketing) {
            array_push($this->allDownloads,$marketing->file_name);
        }

        return view('livewire.marketingFolder.marketing.marketing');
    }

    public function uploadMarketing() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/marketing-maps/'.$this->folderId.'/marketing/upload', navigate: true);
        }
        else {
            return $this->redirect('/marketing-maps', navigate: true);
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
            'route' => 'marketing',
        ];


        $query = http_build_query($params);
        $url = route('download.bulk.zip') . '?' . $query;

        // Livewire 3 event naar frontend
        $this->dispatch('download-zip', url: $url);
    }

    public function downloadAll()
    {
        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->marketing as $marketing) {

                if (!$marketing->file_name) {
                    continue;
                }

                // veilige bestandsnaam voor zip
                $safeName = str_replace(['/', '\\'], '_', $marketing->file_name);

                // Laravel-safe path (beter dan public_path)
                $filePath = Storage::disk('public')->path('marketing/' . $marketing->file_name);

                if (!file_exists($filePath)) {
                    continue;
                }

                $zip->addFileFromPath(
                    $safeName,
                    $filePath
                );
            }

            $zip->finish();

        }, str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $this->folder->name) . '.zip');
    }
}
