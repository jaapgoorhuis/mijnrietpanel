<?php

namespace App\Livewire\DocumentationFolder\Documentation;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;

class
Documentation extends Component
{
    public $documentation;
    public array $selectedDownloads = [];
    public array $allDownloads = [];

    public $locale;
    public $folderId;
    public $folder;

    public function mount($id) {

        $this->folder = \App\Models\DocumentationFolder::find($id);
        $this->folderId = $id;

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->where('lang', 'nl')->where('documentation_category_id', $id)->get();
        } elseif ($this->locale === 'en') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->where('lang','en')->where('documentation_category_id', $id)->get();
        }
    }
    public function render()
    {
        foreach($this->documentation as $documentation) {
            array_push($this->allDownloads,$documentation->file_name);
        }

        return view('livewire.documentationFolder.documentation.documentation');
    }

    public function uploadDocumentation() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/documentation-maps/'.$this->folderId.'/documentation/upload', navigate: true);
        }
        else {
            return $this->redirect('/documentation-maps', navigate: true);
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

    public function downloadAll()
    {
        return response()->streamDownload(function () {

            $zip = new ZipStream();

            foreach ($this->documentation as $documentation) {

                $filePath = public_path('storage/documentation/' . $documentation->file_name);
                $filePath = str_replace(['%20'], ' ', $filePath);

                if (file_exists($filePath)) {

                    // 🧼 veilige filename voor zip
                    $safeName = basename($documentation->file_name);
                    $safeName = str_replace(['/', '\\'], '_', $safeName);

                    $zip->addFileFromPath(
                        $safeName,
                        $filePath
                    );
                }
            }

            $zip->finish();

        }, $this->folder->name . '.zip');
    }
}
