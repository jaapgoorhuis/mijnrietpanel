<?php

namespace App\Livewire\MarketingFolder\Marketing;
use App\Models\Marketing;
use App\Models\MarketingFolder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadMarketing extends Component
{
    use WithFileUploads;

    public $files = [];
    public $marketing;

    public $friendly_name = [];

    public $locale;
    public $folderId;

    public $folder;


    public function mount($id) {
        $this->folderId = $id;
        $this->folder = MarketingFolder::find($id);
    }
    public function render()
    {
        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->marketing = Marketing::orderBy('order_id', 'asc')->where('lang', 'nl')->where('marketing_category_id', $this->folderId)->get();
        } elseif ($this->locale === 'en') {
            $this->marketing = Marketing::orderBy('order_id', 'asc')->where('lang','en')->where('marketing_category_id', $this->folderId)->get();
        }

        return view('livewire.marketingFolder.marketing.uploadMarketing');
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

    public function updateOrder($orderList)
    {
        foreach ($orderList as $item) {
            Marketing::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    public function uploadFiles()
    {
        $this->validate();
        if ($this->files) {
            foreach ($this->files as $file) {
                $file->storeAs(path: 'marketing', name: $file->getClientOriginalName(), options: 'public');
                $latestMarketing = Marketing::orderBy('order_id', 'desc')->first();

                if ($latestMarketing) {
                    $orderId = $latestMarketing->order_id + 1;
                } else {
                    $orderId = 1;
                }


                Marketing::create([
                    'friendly_name' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'order_id' => $orderId,
                    'marketing_category_id' => $this->folderId,
                    'lang' => $this->locale,
                ]);
            }
            session()->flash('success','De bestanden zijn geupload.');
            return $this->redirect('/marketing-maps/'.$this->folderId.'/marketing/upload', navigate: true);
        } else {
            session()->flash('error',  'Upload één of meerdere bestanden.');
        }

    }

    public function remove($id)
    {
        $marketing = Marketing::where('id', $id)->first();
        Storage::disk('public')->delete('marketing/'.$marketing->file_name);

        Marketing::where('id', $id)->delete();
        session()->flash('success', 'Het bestand is verwijderd.');
    }

    public function updateFileName($fileId)
    {
        if ($this->friendly_name) {
            Marketing::where('id', $fileId)->update([
                'friendly_name' => $this->friendly_name[$fileId]
            ]);
            session()->flash('success',  'De bestandsnaam is aangepast.');

        } else {
            session()->flash('error',  'Vul de naam van het bestand in.');
        }
    }
}
