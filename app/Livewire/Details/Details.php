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
}
