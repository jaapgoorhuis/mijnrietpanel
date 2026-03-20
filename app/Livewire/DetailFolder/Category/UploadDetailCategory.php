<?php

namespace App\Livewire\DetailFolder\Category;

use AllowDynamicProperties;
use App\Models\Detail;
use App\Models\DetailFolder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

#[AllowDynamicProperties] class UploadDetailCategory extends Component
{
    use WithFileUploads;

    public $categories = [];
    public $details;

    public $friendly_name = [];
    public $locale;

    // Nieuwe properties voor mapbeheer
    public $newCategoryTitle;
    public $newCategoryImage;
    public $croppedImage;
    public $editingCategoryTitle = [];
    public $selectedCategory = null; // huidige geopende map

    public $files; // voor detail-upload

    public $folder;
    public $folderId;

    public function mount($id)
    {
        $this->locale = config('app.locale');

        $this->folder = DetailFolder::find($id);
        $this->folderId = $id;


        $this->loadCategories();
    }

    public function render()
    {
        if ($this->selectedCategory) {
            $this->details = $this->selectedCategory->details()
                ->where('lang', $this->locale)
                ->orderBy('order_id', 'asc')
                ->get();
        }

        return view('livewire.detailFolders.categories.uploadDetailCategories');
    }

    /*** FOLDER LOGICA ***/
    public function loadCategories()
    {
        $this->categories = \App\Models\DetailCategory::where('lang', $this->locale)
            ->where('detail_folder_id', $this->folderId)
            ->orderBy('order_id', 'asc')
            ->get();
        // Vul de editingFolderTitle array
        $this->editingCategoryTitle = []; // eerst resetten
        foreach ($this->categories as $category) {
            $this->editingCategoryTitle[$category->id] = $category->name; // let op: je gebruikt name in DB
        }
    }

    public function createCategory()
    {
        $this->validate([
            'newCategoryTitle' => 'required|string|max:255',
            'newCategoryImage' => 'file|mimes:jpg,jpeg,png,gif,webp',
        ]);



        $imagePath = null;
        if ($this->newCategoryImage) {
            // Cropped image opslaan
            $imagePath = $this->storeCroppedImage($this->newCategoryImage);
        }

        // Bepaal het hoogste order_id zodat de nieuwe map onderaan komt
        $orderId = \App\Models\DetailCategory::max('order_id') ? \App\Models\DetailCategory::max('order_id') + 1 : 1;

        $category = \App\Models\DetailCategory::create([
            'detail_folder_id' => $this->folderId,
            'name' => $this->newCategoryTitle,
            'cropimage' => $imagePath,
            'order_id' => $orderId,
            'lang' => $this->locale,
        ]);

        // Reset form
        $this->newCategoryTitle = '';
        $this->newCategoryImage = null;
        $this->croppedImage = null;

        // Herlaad de lijst van mappen
        $this->loadCategories();

        session()->flash('success', 'Map is aangemaakt.');
    }


    public function updateCategoryTitle($categoryId)
    {
        $category = \App\Models\DetailCategory::find($categoryId);
        if ($category && isset($this->editingCategoryTitle[$categoryId])) {
            $category->update(['name' => $this->editingCategoryTitle[$categoryId]]);
            session()->flash('success', 'De mapnaam is aangepast.');
        }
    }

    public function updateCategoriesOrder($orderList)
    {
        foreach ($orderList as $item) {
            \App\Models\DetailCategory::where('id', $item['value'])
                ->update(['order_id' => $item['order']]);
        }

        $this->loadCategories(); // herlaad mappen in de juiste volgorde
        session()->flash('success', 'De volgorde is aangepast.');
    }

    public function deleteCategory($categoryId)
    {
        $category = \App\Models\DetailCategory::find($categoryId);
        if ($category) {
            // Verwijder map afbeelding
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }



            foreach ($category->details as $detail) {
                Storage::disk('public')->delete('detailCategory/' . $detail->file_name);
                $detail->delete();
            }

            $category->delete();
            session()->flash('success', 'Map is verwijderd.');
            $this->selectedCategory = null;
            $this->loadCategories();
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = \App\Models\DetailCategory::find($categoryId);
        $this->details = $this->selectedCategory->details()->orderBy('order_id', 'asc')->get();
    }

    /*** DETAIL LOGICA ***/
    protected $rules = [
        'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,bmp,webp',
    ];



    public function messages(): array
    {
        return [
            'newCategoryImage.file' =>  'Het is verplicht om een afbeelding up te loaden.',
            'newCategoryTitle.required' =>  'De map naam is verplicht',
            'newCategoryImage.mimes' =>  'Alle bestanden moeten een afbeelding bestand zijn.',
        ];
    }

    /*** HULP: OPSLAAN VAN CROPPEDE AFBEELDING **/
    protected function storeCroppedImage($image)
    {
        $name = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('categoryImages', $name, 'public');
        return $path;
    }
}
