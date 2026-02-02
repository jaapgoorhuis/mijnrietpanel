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
    public $locale;
    public $friendly_name = [];

    public function render()
    {

        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->where('lang','nl')->get();
        } elseif ($this->locale === 'en') {
            $this->marketing = \App\Models\Marketing::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        return view('livewire.marketing.uploadMarketing');
    }

    protected $rules = [
        'files.*' => 'required|file|mimes:pdf,dwg,jpg,jpeg,png,gif,bmp,webp',
    ];

    public function messages(): array
    {
        return [
            'files.required' => __('messages.Het is verplicht om een bestand te uploaden.'),
            'files.*.mimes' => __('messages.Alle bestanden moeten een PDF, DWG of afbeelding bestand zijn.'),
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
                $latestMarketing = \App\Models\Marketing::orderBy('order_id', 'desc')->first();

                if ($latestMarketing) {
                    $orderId = $latestMarketing->order_id + 1;
                } else {
                    $orderId = 1;
                }
                \App\Models\Marketing::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId,
                    'lang' => $this->locale,
                ]);
            }
            session()->flash('success', __('messages.De bestanden zijn geupload.'));
            return $this->redirect('/marketing/upload', navigate: true);
        } else {
            session()->flash('error', __('messages.Upload één of meerdere bestanden.'));
        }

    }

    public function removeMarketing($id)
    {
        $file =  \App\Models\Marketing::where('id', $id)->first();
        Storage::disk('public')->delete('marketing/'.$file->file_name);
        \App\Models\Marketing::where('id', $id)->delete();
        session()->flash('success', __('messages.Het bestand is verwijderd.'));
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            \App\Models\Marketing::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success', __('messages.De bestandsnaam is aangepast.'));

        } else {
            session()->flash('error', __('messages.Vul de naam van het bestand in.'));
        }
    }
}
