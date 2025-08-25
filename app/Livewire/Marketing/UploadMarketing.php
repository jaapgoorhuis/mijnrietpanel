<?php

namespace App\Livewire\Marketing;


use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadMarketing extends Component
{
    use WithFileUploads;

    public $files = [];
    public $marketing;

    public $friendly_name = [];

    public function render()
    {
        $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->get();
        return view('livewire.marketing.uploadMarketing');
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

    public function updateMarketingOrder($orderList)
    {
        foreach ($orderList as $item) {
            \App\Models\Marketing::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    public function uploadFiles()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'marketing', name: $file->getClientOriginalName(), options: 'public');
                $latestMarketing = \App\Models\Marketing::orderBy('id', 'desc')->first();

                if ($latestMarketing) {
                    $orderId = $latestMarketing->order_id + 1;
                } else {
                    $orderId = 1;
                }
                \App\Models\Marketing::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId
                ]);
            }
            session()->flash('success', 'De bestanden zijn geupload.');
            return $this->redirect('/marketing/upload', navigate: true);
        } else {
            session()->flash('error', 'Upload één of meerdere bestanden.');
        }

    }

    public function removeMarketing($id)
    {
        \App\Models\Marketing::where('id', $id)->delete();
        session()->flash('success', 'Het bestand is verwijderd.');
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            \App\Models\Marketing::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success', 'De bestandsnaam is aangepast.');

        } else {
            session()->flash('error', 'Vul de naam van het bestand in.');
        }
    }
}
