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
    public $selectedFolder = null; // huidige geopende map

    public $files; // voor detail-upload
    public $newImages = [];
    protected $listeners = ['setCroppedImage'];


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
            foreach ($folder->details as $detail) {
                Storage::disk('public')->delete('images/detail-maps/' . $detail->file_name);
                $detail->delete();
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
        'newImages.*' => 'required|file|mimes:jpg,jpeg,png,gif,bmp,webp',
    ];



    public function messages(): array
    {
        return [
            'newFolderImage.file' =>  'Het is verplicht om een afbeelding up te loaden.',
            'newFolderTitle.required' =>  'De map naam is verplicht',
            'newFolderImage.mimes' =>  'Alle bestanden moeten een afbeelding bestand zijn.',
            'newImages.*.mimes' =>  'Alle bestanden moeten een afbeelding bestand zijn.',
        ];
    }

    /*** HULP: OPSLAAN VAN CROPPEDE AFBEELDING **/
    protected function storeCroppedImage($image)
    {
        $name = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('images/detail-maps', $name, 'public');
        return $path;
    }

    public function setCroppedImage($data)
    {
        $this->croppedImage = $data['image'];
    }

    public function updatedNewImages($value, $key)
    {

        $this->validate();

        // $key = categoryId
        $folderId = $key;

        $file = $this->newImages[$folderId];

        if (!$file) return;

        $path = $file->store('images/detail-maps', 'public');

        $folder = \App\Models\DetailFolder::find($folderId);
        $folder->cropimage = $path;
        $folder->save();
        $this->loadFolders();
        session()->flash('success', 'Afbeelding geüpdatet!');
    }
}
