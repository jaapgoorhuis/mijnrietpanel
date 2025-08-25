<?php

namespace App\Livewire\Regulations;

use App\Models\Order;
use App\Models\Regulation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class
Regulations extends Component
{

    public $regulations;

    public function render()
    {
        $this->regulations = Regulation::orderBy('order_id', 'asc')->get();
        return view('livewire.regulations.regulations');
    }

    public function uploadRegulation() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/regulations/upload', navigate: true);
        }
        else {
            return $this->redirect('/regulations', navigate: true);
        }
    }
}
