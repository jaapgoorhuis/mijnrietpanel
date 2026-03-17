<?php

namespace App\Livewire\MarketingFolder;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;

class
MarketingFolder extends Component
{
    public $marketingFolder;

    public $locale;



    public function render()
    {


        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->marketingFolder = \App\Models\MarketingFolder::orderBy('order_id', 'asc')->where('lang', 'nl')->get();
        } elseif ($this->locale === 'en') {
            $this->marketingFolder = \App\Models\MarketingFolder::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        return view('livewire.marketingFolder.marketingFolders');
    }

    public function uploadFolder() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/marketing-maps/upload', navigate: true);
        }
        else {
            return $this->redirect('/marketing-maps', navigate: true);
        }
    }

}
