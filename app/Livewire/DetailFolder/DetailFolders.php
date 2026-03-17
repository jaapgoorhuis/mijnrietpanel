<?php

namespace App\Livewire\DetailFolder;

use App\Models\Detail;
use App\Models\DetailFolder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;

class
DetailFolders extends Component
{
    public $detailFolders;

    public $locale;



    public function render()
    {


        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->detailFolders = DetailFolder::orderBy('order_id', 'asc')->where('lang', 'nl')->get();
        } elseif ($this->locale === 'en') {
            $this->detailFolders = DetailFolder::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        return view('livewire.detailFolders.detailFolders');
    }

    public function uploadDetailFolder() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/detail-maps/upload', navigate: true);
        }
        else {
            return $this->redirect('/detail-maps', navigate: true);
        }
    }

}
