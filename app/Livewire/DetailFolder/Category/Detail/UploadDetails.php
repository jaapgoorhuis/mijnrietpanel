<?php

namespace App\Livewire\DetailFolder\Category\Detail;


use App\Models\Detail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadDetails extends Component
{
    use WithFileUploads;

    public $files = [];
    public $details;

    public $friendly_name = [];

    public $locale;
    public $folderId;


    public function mount($id) {
        $this->folderId = $id;
    }
    public function render()
    {
        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->details = Detail::orderBy('order_id', 'asc')->where('lang', 'nl')->where('detailsFolder_id', $this->folderId)->get();
        } elseif ($this->locale === 'en') {
            $this->details = Detail::orderBy('order_id', 'asc')->where('lang','en')->where('detailsFolder_id', $this->folderId)->get();
        }

        return view('livewire.detailFolders.details.uploadDetails');
    }

    protected $rules = [
        'files.*' => 'required|file|mimes:pdf,dwg,jpg,jpeg,png,gif,bmp,webp',
    ];

    public function messages(): array
    {
        return [
            'files.required' =>  'Het is verplicht om een bestand te uploaden.',
            'files.*.mimes' =>  'Alle bestanden moeten een PDF, DWG of afbeelding bestand zijn.',
        ];
    }

    public function updateDetailsOrder($orderList)
    {
        foreach ($orderList as $item) {
            Detail::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    public function uploadFiles()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'details', name: $file->getClientOriginalName(), options: 'public');
                $latestDetail = Detail::orderBy('order_id', 'desc')->first();

                if ($latestDetail) {
                    $orderId = $latestDetail->order_id + 1;
                } else {
                    $orderId = 1;
                }


                Detail::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId,
                    'detailsFolder_id' => $this->folderId,
                    'lang' => $this->locale,
                ]);
            }
            session()->flash('success','De bestanden zijn geupload.');

            return $this->redirect('/detail-maps/'.$this->folderId.'/details/upload', navigate: true);
        } else {
            session()->flash('error',  'Upload één of meerdere bestanden.');
        }

    }

    public function removeDetail($id)
    {
        $detail = Detail::where('id', $id)->first();
        Storage::disk('public')->delete('details/'.$detail->file_name);

        Detail::where('id', $id)->delete();
        session()->flash('success', __('messages.Het bestand is verwijderd.'));
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            Detail::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success',  __('messages.De bestandsnaam is aangepast.'));

        } else {
            session()->flash('error',  __('messages.Vul de naam van het bestand in.'));
        }
    }
}
