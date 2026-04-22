<?php

namespace App\Livewire\MarketingFolder;

use App\Models\Marketing;
use App\Models\MarketingFolder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
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

    public $newImages = [];
    public $croppedImage;
    public $editingFolderTitle = [];
    public $selectedFolder = null; // huidige geopende map

    public $files; // voor detail-upload

    public $folder;

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
            'newFolderImage' => 'nullable|mimes:jpg,jpeg,png,gif,webp,svg',
        ]);

        $imagePath = null;

        if ($this->newFolderImage) {
            $name = time() . '_' . $this->newFolderImage->getClientOriginalName();
            $imagePath = $this->optimizeImage(
                $this->newFolderImage,
                'marketing/marketing-folder/' . $name,
                800
            );
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

    protected function optimizeImage($file, string $path, int $width = 1200, int $quality = 80): string
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->decodePath($file->getPathname());

        $image->scale(width: $width);

        $encoded = $image->encode(new JpegEncoder($quality));

        Storage::disk('public')->put($path, $encoded);

        return $path;
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
            // Verwijder map afbeelding
            if ($folder->image) {
                Storage::disk('public')->delete($folder->image);
            }


                foreach ($folder->marketing as $marketing) {
                    if ($marketing->file_name && Storage::disk('public')->exists('marketing/' . $marketing->file_name)) {
                        Storage::disk('public')->delete($marketing->file_name);
                    }
                    $marketing->delete();
                }

            $folder->delete();
            session()->flash('success', 'Map is verwijderd.');
            $this->selectedFolder = null;
            $this->loadFolders();
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

    public function updatedNewImages($value, $key)
    {
        $this->saveCategoryImage($key, $value);
    }

    protected function saveCategoryImage($categoryId, $image)
    {
        $this->validate([
            "newImages.$categoryId" => 'required|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        ]);



        $folder = \App\Models\MarketingFolder::find($categoryId);
        if (!$folder) return;

        // Optioneel: oude afbeelding verwijderen
        if ($folder->cropimage) {
            Storage::disk('public')->delete($folder->cropimage);
        }

        $name = time() . '_' . $image->getClientOriginalName();

        $path = $this->optimizeImage(
            $image,
            'marketing/marketing-folder/' . $name,
            800
        );

        $folder->update([
            'cropimage' => $path,
        ]);

        // Live update in frontend
        $this->newImages[$categoryId] = null; // reset file input
        $this->loadFolders(); // herlaad de lijst
        session()->flash('success', 'Afbeelding opgeslagen!');
    }


    /*** HULP: OPSLAAN VAN CROPPEDE AFBEELDING ***/
    protected function storeCroppedImage($image)
    {
        $name = time() . '_' . $image->getClientOriginalName();

        return $this->optimizeImage(
            $image,
            'marketing/marketing-folder/' . $name,
            800
        );
    }
}
