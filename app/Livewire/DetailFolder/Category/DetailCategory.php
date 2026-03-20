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

    public $folderId;

    public $folder;

    public function mount($id) {
        $this->folderId = $id;
    }



    public function render()
    {




        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->detailCategories = \App\Models\DetailCategory::orderBy('order_id', 'asc')->where('lang', 'nl')->get();
        } elseif ($this->locale === 'en') {
            $this->detailCategories = \App\Models\DetailCategory::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        $this->folder = \App\Models\DetailFolder::find($this->folderId);

        return view('livewire.detailFolders.categories.detailCategories');
    }

    public function uploadDetailCategory() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/detail-maps/'.$this->folderId.'/categories/upload', navigate: true);
        }
        else {
            return $this->redirect('/detail-maps/'.$this->folderId, navigate: true);
        }
    }

}
