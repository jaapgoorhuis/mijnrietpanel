<?php

namespace App\Livewire\DetailFolder\Category;

use App\Models\Detail;
use App\Models\DetailFolder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;

class
DetailCategory extends Component
{
    public $detailCategories;

    public $locale;

    public $categoryId;

    public $category;

    public function mount($id) {
        $this->categoryId = $id;
    }



    public function render()
    {




        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->detailCategories = \App\Models\DetailCategory::orderBy('order_id', 'asc')->where('lang', 'nl')->get();
        } elseif ($this->locale === 'en') {
            $this->detailCategories = \App\Models\DetailCategory::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        $this->category = \App\Models\DetailCategory::find($this->categoryId);

        return view('livewire.detailFolders.categories.detailCategories');
    }

    public function uploadDetailCategory() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/detail-maps/'.$this->categoryId.'/categories/upload', navigate: true);
        }
        else {
            return $this->redirect('/detail-maps/'.$this->categoryId, navigate: true);
        }
    }

}
