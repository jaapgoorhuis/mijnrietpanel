<?php

namespace App\Livewire\Companys\Subcontractors;

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

class CreateSubcontractor extends Component
{


    public $company_id;
    public $name;
    public $street;
    public $zipcode;
    public $place;

    public function mount($slug) {
        $this->company_id = $slug;
    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            return view('livewire.companys.subcontractors.createSubcontractor');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:subcontractors,name',
            'street' => 'required',
            'zipcode' => 'required',
            'place' => 'required',
    ];}

    public function messages(): array
    {
        return [
            'name.required' => 'De bedrijfsnaam is verplicht.',
            'street.required' => 'De straat is verplicht.',
            'zipcode.required' => 'De postcode is verplicht.',
            'place.required' => 'De plaats is verplicht.',
            'name.unique' => 'Er bestaat al een bedrijf met deze naam.',

        ];
    }

    public function createSubcontractor() {
        $this->validate($this->rules());

        \App\Models\Subcontractors::create([
            'name' => $this->name,
            'street' => $this->street,
            'zipcode' => $this->zipcode,
            'place' => $this->place,
            'company_id' => $this->company_id,
        ]);
        session()->flash('success','De onderaannemer is aangemaakt');
        return $this->redirect('/companys/'.$this->company_id.'/subcontractors', navigate: true);
    }

    public function cancelCreateSubcontractor() {
        return $this->redirect('/companys/'.$this->company_id.'/subcontractors', navigate: true);
    }
}
