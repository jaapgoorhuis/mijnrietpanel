<?php

namespace App\Livewire\Marketing;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class
Marketing extends Component
{
    public $marketing;
    public array $selectedDownloads = [];
    public function render()
    {
        $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->get();
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
}
