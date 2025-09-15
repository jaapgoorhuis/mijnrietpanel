<?php

namespace App\Livewire\Orders;

use App\Mail\sendOrder;
use App\Models\Application;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderTemplate;
use App\Models\PanelBrand;
use App\Models\PanelLook;
use App\Models\PanelType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderExample extends Component
{



    public function render()
    {
        return view('livewire.orders.example');
    }

    public function genereer() {
        $data = [ 'test' => 'test'];

        Pdf::loadView('pdf.testorder',$data)->save(public_path('/storage/orders/order-test.pdf'));
    }
}
