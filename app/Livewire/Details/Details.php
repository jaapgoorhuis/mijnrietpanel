<?php

namespace App\Livewire\Details;

use App\Models\Detail;
use App\Models\Order;
use App\Models\Regulation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class
Details extends Component
{
    public $details;
    public array $selectedDownloads = [];

    public function render()
    {
        $this->details = Detail::orderBy('order_id', 'asc')->get();
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
}
