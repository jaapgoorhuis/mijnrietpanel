<?php

namespace App\Livewire\sendTestMail;

use App\Mail\sendOrder;
use App\Mail\sendOrderList;
use App\Models\Company;
use App\Models\Offerte;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\Supliers;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class sendTestMail extends Component
{
    public $orders;
    public $editOrderId;

    public array $pakketten = [];


    public function change() {
        dd('updated');
    }

    public function mount() {

        $this->order = Order::orderBy('created_at', 'asc')->first();

        Mail::to('info@crewa.nl')->send(new sendOrderList($this->order));
    }

}
