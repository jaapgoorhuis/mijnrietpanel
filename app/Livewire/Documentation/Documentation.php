<?php

namespace App\Livewire\Documentation;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class
Documentation extends Component
{
    public $documentation;

    public function render()
    {
        $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->get();
        return view('livewire.documentation.documentation');
    }

    public function uploadDocumentation() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/documentation/upload', navigate: true);
        }
        else {
            return $this->redirect('/documentation', navigate: true);
        }
    }
}
