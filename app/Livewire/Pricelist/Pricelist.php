<?php

namespace App\Livewire\Pricelist;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;
use STS\ZipStream\Facades\Zip;

class
Pricelist extends Component
{
    public $pricelist;

    public array $selectedDownloads = [];


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
        $this->pricelist = \App\Models\Pricelist::orderBy('order_id', 'asc')->get();
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


}
