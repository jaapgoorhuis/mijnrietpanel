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

class EditSurcharges extends Component
{
    public $condition;
    public $rule;
    public $number;
    public $price;
    public $name;
    public $surcharge;
    public $surchargeId;


    public function mount($id) {

        $this->surchargeId = $id;
        $this->surcharge = \App\Models\Surcharges::where('id', $id)->first();

        $this->condition = $this->surcharge->condition;
        $this->rule = $this->surcharge->rule;
        $this->number = $this->surcharge->number;
        $this->price = $this->surcharge->price;
        $this->name = $this->surcharge->name;

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.surcharges.editSurcharges');
        } else {

            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('surcharges', 'name')->ignore($this->surchargeId)
            ],

            'price' => 'required',
            'condition' => 'required',
            'rule' => 'required',
            'number' => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'De toeslagnaam is een verplicht veld.',
            'name.unique' => 'Er bestaat al een toeslag met deze naam.',
            'price.required' => 'De prijs is een verplicht veld.',
            'condition.required' => 'De toepassing is een verplicht veld.',
            'rule.required' => 'De regel is een verplicht veld.',
            'number.required' => 'Het aantal is een verplicht veld.',
        ];
    }

    public function updateSurcharge() {

        $this->validate($this->rules());


        \App\Models\Surcharges::where('id', $this->surchargeId)->update([
            'condition' => $this->condition,
            'number' => $this->number,
            'rule' => $this->rule,
            'price' => $this->price,
            'name' => $this->name
        ]);


        session()->flash('success','De toeslag is aangepast');

        return $this->redirect('/surcharges', navigate: true);
    }

    public function cancelEditSurcharge() {
        return $this->redirect('/surcharges', navigate: true);
    }
}
