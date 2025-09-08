<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class CreateCompanys extends Component
{
    public $companys;
    public $bedrijfsnaam;
    public $discount;

    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->companys = Company::get();
            return view('livewire.companys.createCompanys');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function cancelAddCompany() {
        return $this->redirect('/companys', navigate: true);
    }


    protected $rules = [
        'bedrijfsnaam' => 'required|unique:companys',
        'discount' => 'required',

    ];
    public function createCompany() {
        $this->validate();

        Company::create([
            'bedrijfsnaam' => $this->bedrijfsnaam,
            'discount' => $this->discount
        ]);

        session()->flash('success','Het bedrijf is aangemaakt');
        return $this->redirect('/companys', navigate: true);
    }

}
