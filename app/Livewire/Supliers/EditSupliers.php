<?php

namespace App\Livewire\Supliers;

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

class EditSupliers extends Component
{
    public $name;
    public $suplier_name;
    public $suplier_straat;
    public $suplier_plaats;
    public $suplier_land;
    public $suplier_postcode;
    public $status;
    public $werkende_breedte;
    public $toepassing_dak;
    public $toepassing_wand;

    public $suplierId;
    public $suplier;
    public $suplier_email;
    public function mount($id) {

        $this->suplierId = $id;
        $this->suplier = \App\Models\Supliers::where('id', $id)->first();
         $this->suplier_name = $this->suplier->suplier_name;
         $this->suplier_straat = $this->suplier->suplier_straat;
         $this->suplier_plaats = $this->suplier->suplier_plaats;
         $this->suplier_land = $this->suplier->suplier_land;
         $this->suplier_postcode = $this->suplier->suplier_postcode;
         $this->status = $this->suplier->status;
         $this->werkende_breedte = $this->suplier->werkende_breedte;
         $this->toepassing_dak = $this->suplier->toepassing_dak;
         $this->toepassing_wand = $this->suplier->toepassing_wand;
         $this->name = $this->suplier->name;
         $this->suplier_email = $this->suplier->suplier_email;


    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.supliers.editSupliers');
        } else {

            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'suplier_name' => [
                'required',
                Rule::unique('supliers', 'suplier_name')->ignore($this->suplierId)
            ],
            'suplier_straat' => 'required',
            'suplier_land' => 'required',
            'suplier_postcode' => 'required',
            'suplier_plaats' => 'required',
            'status' => 'required',
            'werkende_breedte' => 'required',
            'toepassing_dak' => 'required',
            'toepassing_wand' => 'required',
            'name' => 'required',
            'suplier_email' => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'suplier_name.required' => 'De naam van de leverancier is een verplicht veld.',
            'suplier_name.unique' => 'Er bestaat al een leverancier met deze naam.',
            'suplier_straat.required' => 'De straat van de leverancier is een verplicht veld.',
            'suplier_land.required' => 'Het land van de leverancier is een verplicht veld.',
            'suplier_postcode.required' => 'De postcode van de leverancier is een verplicht veld.',
            'suplier_plaats.required' => 'De plaats van de leverancier is een verplicht veld.',
            'status.required' => 'De status is een verplicht veld.',
            'werkende_breedte.required' => 'De werkende breedte is een verplicht veld.',
            'toepassing_dak.required' => 'Dit is een verplicht veld.',
            'toepassing_wand.required' => 'Dit is een verplicht veld.',
            'name.required' => 'De naam van het paneel is een verplicht veld.',
            'suplier_email.required' => 'Het emailadres van de leverancier is een verplicht veld.',
        ];
    }

    public function editSuplier() {

        $this->validate($this->rules());


        \App\Models\Supliers::where('id', $this->suplierId)->update([
            'suplier_name' => $this->suplier_name,
            'suplier_straat' => $this->suplier_straat,
            'suplier_land' => $this->suplier_land,
            'suplier_postcode' => $this->suplier_postcode,
            'suplier_plaats' => $this->suplier_plaats,
            'status' => $this->status,
            'werkende_breedte' => $this->werkende_breedte,
            'toepassing_dak' => $this->toepassing_dak,
            'toepassing_wand' => $this->toepassing_wand,
            'name' => $this->name,
            'suplier_email' => $this->suplier_email,
        ]);


        session()->flash('success','De leverancier is aangepast');

        return $this->redirect('/supliers', navigate: true);
    }

    public function cancelEditSuplier() {
        return $this->redirect('/supliers', navigate: true);
    }
}
