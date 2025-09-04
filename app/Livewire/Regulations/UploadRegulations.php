<?php

namespace App\Livewire\Regulations;


use App\Models\Regulation;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadRegulations extends Component
{
    use WithFileUploads;

    public $files = [];
    public $regulations;

    public $friendly_name = [];

    public function render()
    {
        $this->regulations = Regulation::orderBy('order_id', 'asc')->get();
        return view('livewire.regulations.uploadRegulations');
    }

    protected $rules = [
        'files.*' => 'required|file|mimes:pdf|max:2048',
    ];

    public function messages(): array
    {
        return [
            'files.required' => 'Het is verplicht om een bestand te uploaden.',
            'files.*.mimes' => 'Alle bestanden moeten een PDF bestand zijn.',
            'files.*.max' => 'Alle bestanden mogen niet groter dan 2MB zijn.',
        ];
    }

    public function updateRegulationsOrder($orderList)
    {
        foreach ($orderList as $item) {
            Regulation::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    public function uploadFiles()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'regulations', name: $file->getClientOriginalName(), options: 'public');
                $latestRegulation = Regulation::orderBy('id', 'desc')->first();

                if ($latestRegulation) {
                    $orderId = $latestRegulation->order_id + 1;
                } else {
                    $orderId = 1;
                }
                Regulation::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId
                ]);
            }
            session()->flash('success', 'De bestanden zijn geupload.');
            return $this->redirect('/regulations/upload', navigate: true);
        } else {
            session()->flash('error', 'Upload één of meerdere bestanden.');
        }

    }

    public function removeRegulation($id)
    {
        $file = Regulation::where('id', $id)->first();
        Storage::disk('public')->delete('regulations/'.$file->file_name);
        Regulation::where('id', $id)->delete();
        session()->flash('success', 'Het bestand is verwijderd.');
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            Regulation::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success', 'De bestandsnaam is aangepast.');

        } else {
            session()->flash('error', 'Vul de naam van het bestand in.');
        }
    }
}
