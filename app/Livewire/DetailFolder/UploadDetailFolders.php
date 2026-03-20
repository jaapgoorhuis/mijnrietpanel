<?php

namespace App\Livewire\DetailFolder;

use App\Models\Detail;
use App\Models\DetailFolder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadDetailFolders extends Component
{
    use WithFileUploads;

    public $folders = [];
    public $details;

    public $friendly_name = [];
    public $locale;

    // Nieuwe properties voor mapbeheer
    public $newFolderTitle;
    public $newFolderImage;
    public $croppedImage;
    public $editingFolderTitle = [];

    public $newImages = [];
    public $selectedFolder = null; // huidige geopende map

    public $files; // voor detail-upload

    public function mount()
    {
        $this->locale = config('app.locale');


        $this->loadFolders();
    }

    public function render()
    {
        if ($this->selectedFolder) {
            $this->details = $this->selectedFolder->details()
                ->where('lang', $this->locale)
                ->orderBy('order_id', 'asc')
                ->get();
        }

        return view('livewire.detailFolders.uploadDetailFolders');
    }

    /*** FOLDER LOGICA ***/
    public function loadFolders()
    {
        $this->folders = DetailFolder::where('lang', $this->locale)
            ->orderBy('order_id', 'asc')
            ->get();

        // Vul de editingFolderTitle array
        $this->editingFolderTitle = []; // eerst resetten
        foreach ($this->folders as $folder) {
            $this->editingFolderTitle[$folder->id] = $folder->name; // let op: je gebruikt name in DB
        }
    }

    public function createFolder()
    {
        $this->validate([
            'newFolderTitle' => 'required|string|max:255',
            'newFolderImage' => 'file|mimes:jpg,jpeg,png,gif,webp',
        ]);



        $imagePath = null;
        if ($this->newFolderImage) {
            // Cropped image opslaan
            $imagePath = $this->storeCroppedImage($this->newFolderImage);
        }

        // Bepaal het hoogste order_id zodat de nieuwe map onderaan komt
        $orderId = DetailFolder::max('order_id') ? DetailFolder::max('order_id') + 1 : 1;

        $folder = DetailFolder::create([
            'name' => $this->newFolderTitle,
            'cropimage' => $imagePath,
            'order_id' => $orderId,
            'lang' => $this->locale,
        ]);

        // Reset form
        $this->newFolderTitle = '';
        $this->newFolderImage = null;
        $this->croppedImage = null;

        // Herlaad de lijst van mappen
        $this->loadFolders();

        session()->flash('success', 'Map is aangemaakt.');
    }


    public function updateFolderTitle($folderId)
    {
        $folder = DetailFolder::find($folderId);
        if ($folder && isset($this->editingFolderTitle[$folderId])) {
            $folder->update(['name' => $this->editingFolderTitle[$folderId]]);
            session()->flash('success', 'De mapnaam is aangepast.');
        }
    }

    public function updateFoldersOrder($orderList)
    {
        foreach ($orderList as $item) {
            DetailFolder::where('id', $item['value'])
                ->update(['order_id' => $item['order']]);
        }

        $this->loadFolders(); // herlaad mappen in de juiste volgorde
        session()->flash('success', 'De volgorde is aangepast.');
    }

    public function deleteFolder($folderId)
    {
        $folder = DetailFolder::find($folderId);
        if ($folder) {
            // Verwijder map afbeelding
            if ($folder->image) {
                Storage::disk('public')->delete($folder->image);
            }

            // Verwijder alle details in de folder
            foreach ($folder->detailCategories as $category) {
                Storage::disk('public')->delete('details/detail-categories/' . $category->file_name);
                $category->delete();
            }

            foreach ($folder->detailCategories as $category) {
                foreach ($category->details as $detail) {
                    if ($detail->file_name && Storage::disk('public')->exists('details/' . $detail->file_name)) {
                        Storage::disk('public')->delete('details/' . $detail->file_name);
                    }
                    $detail->delete();
                }
            }

            $folder->delete();
            session()->flash('success', 'Map is verwijderd.');
            $this->selectedFolder = null;
            $this->loadFolders();
        }
    }

    public function selectFolder($folderId)
    {
        $this->selectedFolder = DetailFolder::find($folderId);
        $this->details = $this->selectedFolder->details()->orderBy('order_id', 'asc')->get();
    }

    /*** DETAIL LOGICA ***/
    protected $rules = [
        'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,bmp,webp',
    ];

    public function updatedNewImages($value, $key)
    {

        $this->saveCategoryImage($key, $value);
    }

    protected function saveCategoryImage($categoryId, $image)
    {
        $this->validate([
            "newImages.$categoryId" => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $folder = \App\Models\DetailFolder::find($categoryId);
        if (!$folder) return;

        // Optioneel: oude afbeelding verwijderen
        if ($folder->cropimage) {
            Storage::disk('public')->delete($folder->cropimage);
        }

        $name = time() . '_' . $image->getClientOriginalName();
        $path = $image->storeAs('details/detail-folder', $name, 'public');

        $folder->update([
            'cropimage' => $path,
        ]);

        // Live update in frontend
        $this->newImages[$categoryId] = null; // reset file input
        $this->loadFolders(); // herlaad de lijst
        session()->flash('success', 'Afbeelding opgeslagen!');
    }



    public function messages(): array
    {
        return [
            'newFolderImage.file' =>  'Het is verplicht om een afbeelding up te loaden.',
            'newFolderTitle.required' =>  'De map naam is verplicht',
            'newFolderImage.mimes' =>  'Alle bestanden moeten een afbeelding bestand zijn.',
        ];
    }

    /*** HULP: OPSLAAN VAN CROPPEDE AFBEELDING **/
    protected function storeCroppedImage($image)
    {
        $name = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('details/detail-folder', $name, 'public');
        return $path;
    }
}
