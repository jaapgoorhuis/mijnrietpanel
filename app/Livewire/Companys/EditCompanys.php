<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class EditCompanys extends Component
{
    public $company;
    public $bedrijfsnaam;
    public $discount;

    public function mount($id) {
        $this->company = Company::where('id', $id)->first();

        $this->bedrijfsnaam = $this->company->bedrijfsnaam;
        $this->discount = $this->company->discount;
    }

    public function render()
    {
        if(Auth::user()->is_admin) {

            return view('livewire.companys.editCompanys');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function cancelEditCompany() {
        return $this->redirect('/companys', navigate: true);
    }


    public function rules(): array
    {
        return [
            'bedrijfsnaam' => 'required|unique:companys,bedrijfsnaam,'.$this->company->id,

        ];
    }

    public function messages(): array
    {
        return [
            'bedrijfsnaam.required' => 'De bedrijfsnaam is een verplicht veld.',
            'bedrijfsnaam.unique' => 'Er bestaat al een bedrijf met deze naam.',

        ];
    }

    public function updateCompany($id) {
        $this->validate($this->rules());

        Company::where('id', $id)->update([
            'bedrijfsnaam' => $this->bedrijfsnaam,
            'discount' => $this->discount
        ]);

        session()->flash('success','Het bedrijf is aangepast');
        return $this->redirect('/companys', navigate: true);
    }

}
