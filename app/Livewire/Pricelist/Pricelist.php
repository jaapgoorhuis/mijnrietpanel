<?php

namespace App\Livewire\Pricelist;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class
Pricelist extends Component
{
    public $pricelist;

    public function render()
    {
        $this->pricelist = \App\Models\Pricelist::orderBy('order_id', 'asc')->get();
        return view('livewire.pricelist.pricelist');
    }

    public function uploadPricelist() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/pricelist/upload', navigate: true);
        }
        else {
            return $this->redirect('/pricelist', navigate: true);
        }
    }
}
