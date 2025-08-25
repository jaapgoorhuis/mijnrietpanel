<?php

namespace App\Livewire\Marketing;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class
Marketing extends Component
{
    public $marketing;

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
}
