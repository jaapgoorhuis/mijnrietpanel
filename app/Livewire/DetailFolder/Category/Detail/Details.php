<?php

namespace App\Livewire\DetailFolder\Category\Detail;

use App\Models\Detail;
use App\Models\DetailCategory;
use App\Models\DetailFolder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
    public $category;
    public $categoryId;

    public function mount($id,$category) {


        $this->categoryId = $category;

        $this->folder = DetailFolder::find($id);
        $this->category = DetailCategory::find($category);
        $this->folderId = $id;

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->details = Detail::orderBy('order_id', 'asc')->where('lang', 'nl')->where('detail_category_id', $category)->get();
        } elseif ($this->locale === 'en') {
            $this->details = Detail::orderBy('order_id', 'asc')->where('lang','en')->where('detail_category_id', $category)->get();
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
            return $this->redirect('/detail-maps/'.$this->folderId.'/categories/'.$this->categoryId.'/details/upload', navigate: true);
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

    public function downloadAll()
    {
        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->details as $detail) {

                if (!$detail->file_name) {
                    continue;
                }

                // veilige ZIP naam
                $safeName = str_replace(['/', '\\'], '_', $detail->file_name);

                $filePath = Storage::disk('public')->path('details/' . $detail->file_name);

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
