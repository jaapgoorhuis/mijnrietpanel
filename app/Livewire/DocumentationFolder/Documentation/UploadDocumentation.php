<?php

namespace App\Livewire\DocumentationFolder\Documentation;

use App\Models\DocumentationFolder;
use App\Models\Marketing;
use App\Models\MarketingFolder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadDocumentation extends Component
{
    use WithFileUploads;

    public $file;
    public $documentation;

    public $friendly_name = [];

    public $locale;
    public $folderId;

    public $folder;

    public $cropimage = []; // bestaande afbeeldingen per item
    public $newCropimage; // nieuwe afbeelding bij upload

    public function mount($id)
    {
        $this->folderId = $id;
        $this->folder = DocumentationFolder::find($id);
    }

    public function render()
    {
        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')
                ->where('lang', 'nl')
                ->where('documentation_category_id', $this->folderId)
                ->get();
        } elseif ($this->locale === 'en') {
            $this->documentation = \App\Models\Documentation::orderBy('order_id', 'asc')
                ->where('lang', 'en')
                ->where('documentation_category_id', $this->folderId)
                ->get();
        }

        foreach ($this->documentation as $item) {
            if (!isset($this->friendly_name[$item->id])) {
                $this->friendly_name[$item->id] = $item->friendly_name;
            }
        }

        return view('livewire.documentationFolder.documentation.uploadDocumentation');
    }

    protected $rules = [
        'cropimage.*' => 'mimes:jpg,svg,jpeg,png,gif,webp', // max 2MB
        'newCropimage' => 'nullable|mimes:jpg,svg,jpeg,png,gif,webp', // nieuwe afbeelding bij upload
    ];

    public function messages(): array
    {
        return [
            'file.required' =>  'Het is verplicht om een bestand te uploaden.',
            'file.mimes' =>  'Alle bestanden moeten een PDF, DWG, OTF, REVIT of afbeelding bestand zijn.',
            'cropimage.*.mimes' => 'Het bestand moet een afbeelding zijn',
            'newCropimage.image' => 'Het nieuwe bestand moet een afbeelding zijn',
            'newCropimage.max' => 'De afbeelding mag maximaal 2MB zijn',
            'newCropimage.required' => 'De tumbnail is verplicht',
        ];
    }

    public function updateOrder($orderList)
    {
        foreach ($orderList as $item) {
            \App\Models\Documentation::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }



    public function uploadFiles()
    {
        $this->validate();

        if ($this->file) {

            $allowed = ['pdf','jpg','jpeg','png','gif','bmp','webp','svg','otf','dwg','rvt','rfa'];
            $extension = strtolower($this->file->getClientOriginalExtension());

            if (!in_array($extension, $allowed)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'files' => 'Bestandstype niet toegestaan.',
                ]);
            }

            $manager = new ImageManager(new Driver());

            $safeName = uniqid() . '_' . $this->file->getClientOriginalName();
            $path = 'documentation/' . $safeName;

            $isImage = in_array($extension, ['jpg','jpeg','png','gif','webp','bmp']);

            if ($isImage) {

                $image = $manager->decodePath($this->file->getPathname());
                $image->scale(width: 1200);

                $encoded = $image->encode(new JpegEncoder(quality: 80));

                Storage::disk('public')->put($path, $encoded);

            } else {

                $this->file->storeAs('documentation', $safeName, 'public');
            }

            $latest = \App\Models\Documentation::orderBy('order_id', 'desc')->first();
            $orderId = $latest ? $latest->order_id + 1 : 1;

            $documentation = \App\Models\Documentation::create([
                'friendly_name' => $this->file->getClientOriginalName(),
                'file_name' => $safeName,
                'order_id' => $orderId,
                'documentation_category_id' => $this->folderId,
                'lang' => $this->locale,
            ]);

            // 🖼 CROPPED IMAGE
            if ($this->newCropimage) {

                $cropName = uniqid() . '_crop_' . $this->newCropimage->getClientOriginalName();
                $cropPath = 'documentation/' . $cropName;

                $cropImage = $manager->decodePath($this->newCropimage->getPathname());
                $cropImage->scale(width: 800);

                $encoded = $cropImage->encode(new JpegEncoder(80));

                Storage::disk('public')->put($cropPath, $encoded);

                $documentation->cropimage = $cropName;
                $documentation->save();
            }

            session()->flash('success', 'De bestanden zijn geupload.');

            $this->reset(['file', 'newCropimage']);

            return $this->redirect('/documentation-maps/' . $this->folderId . '/documentation/upload', navigate: true);
        }

        session()->flash('error', 'Upload één of meerdere bestanden.');
    }


    public function remove($id)
    {
        $documentation = \App\Models\Documentation::where('id', $id)->first();

        // verwijder bestand
        Storage::disk('public')->delete('documentation/' . $documentation->file_name);

        // verwijder cropimage indien aanwezig
        if ($documentation->cropimage) {
            Storage::disk('public')->delete($documentation->cropimage);
        }

        // delete record
        \App\Models\Documentation::where('id', $id)->delete();

        session()->flash('success', 'Het bestand is verwijderd.');
    }

    public function updateItem($id)
    {
        $documentation = \App\Models\Documentation::find($id);

        // Naam updaten
        if(isset($this->friendly_name[$id])) {
            $documentation->friendly_name = $this->friendly_name[$id];
        }

        // Cropimage per item updaten
        if (isset($this->cropimage[$id])) {
            if($documentation->cropimage) {
                Storage::disk('public')->delete($documentation->cropimage);
            }

            $path = $this->cropimage[$id]->store('documentation', 'public');
            $documentation->cropimage = $path;

            // reset file input van dit item
            unset($this->cropimage[$id]);
        }

        $documentation->save();

        session()->flash('success', 'Item succesvol bijgewerkt');

        // reset het file input van dit item
        unset($this->cropimage[$id]);
    }
}
