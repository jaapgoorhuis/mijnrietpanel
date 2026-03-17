<?php

namespace App\Livewire\MarketingFolder;

use App\Models\Marketing;
use App\Models\MarketingFolder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadMarketingFolder extends Component
{
    use WithFileUploads;

    public $folders = [];
    public $marketing = []; // initialiseer altijd als lege array

    public $friendly_name = [];
    public $locale;

    // Nieuwe properties voor mapbeheer
    public $newFolderTitle;
    public $newFolderImage;
    public $croppedImage;
    public $editingFolderTitle = [];
    public $selectedFolder = null; // huidige geopende map

    public $files; // voor detail-upload

    public function mount()
    {
        $this->locale = config('app.locale');
        $this->marketing = collect(); // altijd een lege collectie
        $this->loadFolders();
    }

    public function render()
    {
        // Marketing items ophalen als er een geselecteerde folder is
        if ($this->selectedFolder) {
            $this->marketing = $this->selectedFolder->marketing()
                ->where('lang', $this->locale)
                ->orderBy('order_id', 'asc')
                ->get();
        }

        return view('livewire.marketingFolder.uploadMarketingFolders');
    }

    /*** FOLDER LOGICA ***/
    public function loadFolders()
    {
        $this->folders = MarketingFolder::where('lang', $this->locale)
            ->orderBy('order_id', 'asc')
            ->get();

        // Vul de editingFolderTitle array
        $this->editingFolderTitle = [];
        foreach ($this->folders as $folder) {
            $this->editingFolderTitle[$folder->id] = $folder->name;
        }
    }

    public function createFolder()
    {
        $this->validate([
            'newFolderTitle' => 'required|string|max:255',
            'newFolderImage' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
        ]);

        $imagePath = null;
        if ($this->newFolderImage) {
            $imagePath = $this->storeCroppedImage($this->newFolderImage);
        }

        $orderId = MarketingFolder::max('order_id') ? MarketingFolder::max('order_id') + 1 : 1;

        MarketingFolder::create([
            'name' => $this->newFolderTitle,
            'cropimage' => $imagePath,
            'order_id' => $orderId,
            'lang' => $this->locale,
        ]);

        $this->newFolderTitle = '';
        $this->newFolderImage = null;
        $this->croppedImage = null;

        $this->loadFolders();

        session()->flash('success', 'Map is aangemaakt.');
    }

    public function updateFolderTitle($folderId)
    {
        $folder = MarketingFolder::find($folderId);
        if ($folder && isset($this->editingFolderTitle[$folderId])) {
            $folder->update(['name' => $this->editingFolderTitle[$folderId]]);
            session()->flash('success', 'De mapnaam is aangepast.');
        }
    }

    public function updateFoldersOrder($orderList)
    {
        foreach ($orderList as $item) {
            MarketingFolder::where('id', $item['value'])
                ->update(['order_id' => $item['order']]);
        }

        $this->loadFolders();
        session()->flash('success', 'De volgorde is aangepast.');
    }

    public function deleteFolder($folderId)
    {
        $folder = MarketingFolder::find($folderId);
        if ($folder) {
            if ($folder->cropimage) {
                Storage::disk('public')->delete($folder->cropimage);
            }

            foreach ($folder->marketing as $marketingItem) {
                Storage::disk('public')->delete('marketingFolders/' . $marketingItem->file_name);
                $marketingItem->delete();
            }

            $folder->delete();
            $this->selectedFolder = null;
            $this->marketing = collect(); // reset marketing items
            $this->loadFolders();

            session()->flash('success', 'Map is verwijderd.');
        }
    }

    public function selectFolder($folderId)
    {
        $this->selectedFolder = MarketingFolder::find($folderId);

        if ($this->selectedFolder) {
            $this->marketing = $this->selectedFolder->marketing()
                ->where('lang', $this->locale)
                ->orderBy('order_id', 'asc')
                ->get();
        } else {
            $this->marketing = collect(); // fallback
        }
    }

    /*** DETAIL LOGICA ***/
    protected $rules = [
        'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,bmp,webp',
    ];

    public function messages(): array
    {
        return [
            'newFolderImage.file' =>  'Het is verplicht om een afbeelding up te loaden.',
            'newFolderTitle.required' =>  'De map naam is verplicht',
            'newFolderImage.mimes' =>  'Alle bestanden moeten een afbeelding bestand zijn.',
        ];
    }

    /*** HULP: OPSLAAN VAN CROPPEDE AFBEELDING ***/
    protected function storeCroppedImage($image)
    {
        $name = time() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('folderImages', $name, 'public');
    }
}
