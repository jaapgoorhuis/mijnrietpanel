<?php

namespace App\Livewire\MyCompany;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;


class MyCompany extends Component
{
    use WithFileUploads;

    public $company;

    public $files =[];

    public $straat;
    public $postcode;
    public $plaats;

    public function render()
    {
        $this->company = Auth::user()->companys->first();

        $this->straat = $this->company->straat;
        $this->postcode = $this->company->postcode;
        $this->plaats = $this->company->plaats;

            return view('livewire.myCompany.myCompany');

    }

    protected $rules = [
        'files.*' => 'required|image|mimes:jpeg,jpg,png,webP,svg|max:2048',
    ];

    public function messages(): array
    {
        return [
            'files.required' => 'Het is verplicht om een bestand te uploaden.',
            'files.*.mimes' => 'Alle bestanden moeten een afbeeldingbestand zijn.',
            'files.*.max' => 'Alle bestanden mogen niet groter dan 2MB zijn.',
        ];
    }


    public function uploadCompanyLogo()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'companyLogos', name: $file->getClientOriginalName(), options: 'public');

                \App\Models\Company::where('id', Auth::user()->companys->id)->update([
                    'logo' => $file->getClientOriginalName(),

                ]);
            }
            session()->flash('success', 'Het logo is geupload.');
            return $this->redirect('/mycompany', navigate: true);
        } else {
            session()->flash('error', 'Upload één afbeelding.');
        }

    }

    public function priceRules() {
        return $this->redirect('/companys/pricerules', navigate: true);
    }

    public function updateCompany() {

        $this->validate([
            'straat' => 'required',
            'postcode' => 'required',
            'plaats' => 'required',
        ],
            [
                'straat.required' => 'De straat is verplicht.',
                'postcode.required' => 'De postcode is verplicht.',
                'plaats.required' => 'De plaats is verplicht.',
            ]
        );

            Company::where('id', $this->company->id)->update(
                [
                    'straat' => $this->straat,
                    'postcode' => $this->postcode,
                    'plaats' => $this->plaats
                ]
            );
            session()->flash('success', 'Het bedrijf is geupdate.');
            return $this->redirect('/mycompany', navigate: true);

    }


}
