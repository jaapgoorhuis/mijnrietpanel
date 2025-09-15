<?php

namespace App\Livewire\Surcharges;

use App\Mail\sendUpdatedUser;
use App\Models\Application;
use App\Models\PanelBrand;
use App\Models\PanelLook;
use App\Models\PanelType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateSurcharges extends Component
{


    public $condition = 'onder';
    public $rule = 'vierkantemeter';
    public $number;
    public $price;
    public $name;


    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.surcharges.createSurcharges');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'condition' => 'required',
            'number' => 'required',
            'rule' => 'required|unique:surcharges,rule',
            'price' => 'required',
            'name' => 'required|unique:surcharges,name'
    ];}

    public function messages(): array
    {
        return [
            'condition.required' => 'De toepassing is een verplicht veld.',
            'number.required' => 'Het aantal is een verplicht veld.',
            'rule.unique' => 'De regel moet uniek zijn. Er bestaat al een regel.',
            'rule.required' => 'De regel is een verplicht veld.',
            'price.required' => 'De prijs is een verplicht veld.',
            'name.required' => 'De toeslag naam is verplicht.',
            'name.unique' => 'Er bestaat al een toeslag met deze benaming.',
        ];
    }

    public function createSurcharge() {
        $this->validate($this->rules());

        \App\Models\Surcharges::create([
            'condition' => $this->condition,
            'number' => $this->number,
            'rule' => $this->rule,
            'price' => $this->price,
            'name' => $this->name
        ]);



        return $this->redirect('/surcharges', navigate: true);
    }

    public function cancelCreateSurcharge() {
        return $this->redirect('/surcharges', navigate: true);
    }
}
