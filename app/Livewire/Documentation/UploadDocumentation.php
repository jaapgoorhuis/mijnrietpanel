<?php

namespace App\Livewire\Documentation;


use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadDocumentation extends Component
{
    use WithFileUploads;

    public $files = [];
    public $documentation;

    public $friendly_name = [];

    public $locale;
    public function render()
    {

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->where('lang','nl')->get();
        } elseif ($this->locale === 'en') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        return view('livewire.documentation.uploadDocumentation');
    }

    protected $rules = [
        'files.*' => 'required|file|mimes:pdf,dwg,jpg,jpeg,png,gif,bmp,webp',
    ];

    public function messages(): array
    {
        return [
            'files.required' =>  __('messages.Het is verplicht om een bestand te uploaden.'),
            'files.*.mimes' =>  __('messages.Alle bestanden moeten een PDF, DWG of afbeelding bestand zijn.'),
        ];
    }

    public function updateDocumentationOrder($orderList)
    {
        foreach ($orderList as $item) {
            \App\Models\Documentation::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    public function uploadFiles()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'documentation', name: $file->getClientOriginalName(), options: 'public');
                $latestDocumentation = \App\Models\Documentation::orderBy('order_id', 'desc')->first();

                if ($latestDocumentation) {
                    $orderId = $latestDocumentation->order_id + 1;
                } else {
                    $orderId = 1;
                }
                \App\Models\Documentation::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId,
                    'lang' => $this->locale,
                ]);
            }
            session()->flash('success', __('messages.De bestanden zijn geupload.'));
            return $this->redirect('/documentation/upload', navigate: true);
        } else {
            session()->flash('error',  __('messages.Upload één of meerdere bestanden.'));
        }
    }

    public function removeDocumentation($id)
    {
        $file = \App\Models\Documentation::where('id', $id)->first();
        Storage::disk('public')->delete('documentation/'.$file->file_name);
        \App\Models\Documentation::where('id', $id)->delete();
        session()->flash('success',  __('messages.Het bestand is verwijderd.'));
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            \App\Models\Documentation::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success',  __('messages.De bestandsnaam is aangepast.'));

        } else {
            session()->flash('error',  __('messages.Vul de naam van het bestand in.'));
        }
    }
}
