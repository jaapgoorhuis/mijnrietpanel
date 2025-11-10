<?php

namespace App\Livewire\Companys\Subcontractors;

use App\Mail\sendUpdatedUser;
use App\Models\Application;
use App\Models\Company;
use App\Models\PanelBrand;
use App\Models\PanelLook;
use App\Models\PanelType;
use App\Models\Subcontractors;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditSubcontractor extends Component
{

    public $company_id;

    public $id;
    public $name;
    public $subcontractor;

    public function mount($id, $slug) {


        $this->company_id = $slug;
        $this->id = $id;
        $this->subcontractor = Subcontractors::where('id', $this->id)->first();
        $this->name = $this->subcontractor->name;


    }
    public function render()
    {
        return view('livewire.companys.subcontractors.editSubcontractor');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('subcontractors', 'name')->ignore($this->id)->where(function ($query) {
                    return $query->where('company_id', $this->company_id);})
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'De bedrijfsnaam is een verplicht veld.',
            'name.unique' => 'Er bestaat al een bedrijf met deze naam.',

        ];
    }

    public function saveSubcontractor() {

        $this->validate($this->rules());


        \App\Models\Subcontractors::where('id', $this->id)->update([
            'name' => $this->name,

        ]);


        session()->flash('success','De onderaannamer is aangepast');

        return $this->redirect('/companys/'.$this->company_id.'/subcontractors', navigate: true);
    }

    public function cancelEditSubcontractor() {
        return $this->redirect('/companys/'.$this->company_id.'/subcontractors', navigate: true);
    }
}
