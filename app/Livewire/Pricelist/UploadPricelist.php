<?php

namespace App\Livewire\Pricelist;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadPricelist extends Component
{
    use WithFileUploads;

    public $files = [];
    public $pricelist;

    public $friendly_name = [];


    public function mount() {
        if(Auth::user()->is_admin) {
            return view('livewire.pricelist.uploadPricelist');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function render()
    {
        $this->pricelist = \App\Models\Pricelist::orderBy('order_id', 'asc')->get();
        return view('livewire.pricelist.uploadPricelist');
    }

    protected $rules = [
        'files.*' => 'required|file|mimes:pdf,dwg,jpg,jpeg,png,gif,bmp,webp',
    ];

    public function messages(): array
    {
        return [
            'files.required' => 'Het is verplicht om een bestand te uploaden.',
            'files.*.mimes' => 'Alle bestanden moeten een PDF, DWG of afbeelding bestand zijn.',
        ];
    }

    public function updatePricelistOrder($orderList)
    {
        foreach ($orderList as $item) {
            \App\Models\Pricelist::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    public function uploadFiles()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'pricelist', name: $file->getClientOriginalName(), options: 'public');
                $latestPricelist = \App\Models\Pricelist::orderBy('order_id', 'desc')->first();

                if ($latestPricelist) {
                    $orderId = $latestPricelist->order_id + 1;
                } else {
                    $orderId = 1;
                }
                \App\Models\Pricelist::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId
                ]);
            }
            session()->flash('success', 'De bestanden zijn geupload.');
            return $this->redirect('/pricelist/upload', navigate: true);
        } else {
            session()->flash('error', 'Upload één of meerdere bestanden.');
        }

    }

    public function removePricelist($id)
    {
        $file = \App\Models\Pricelist::where('id', $id)->first();
        Storage::disk('public')->delete('pricelist/'.$file->file_name);
        \App\Models\Pricelist::where('id', $id)->delete();
        session()->flash('success', 'Het bestand is verwijderd.');
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            \App\Models\Pricelist::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success', 'De bestandsnaam is aangepast.');

        } else {
            session()->flash('error', 'Vul de naam van het bestand in.');
        }
    }
}
