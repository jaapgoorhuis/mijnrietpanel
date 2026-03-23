<?php

namespace App\Livewire\DocumentationFolder;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use ZipStream\ZipStream;

class
DocumentationFolder extends Component
{
    public $documentationFolder;

    public $locale;



    public function render()
    {


        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->documentationFolder = \App\Models\DocumentationFolder::orderBy('order_id', 'asc')->where('lang', 'nl')->get();
        } elseif ($this->locale === 'en') {
            $this->documentationFolder = \App\Models\DocumentationFolder::orderBy('order_id', 'asc')->where('lang','en')->get();
        }

        return view('livewire.documentationFolder.documentationFolders');
    }

    public function uploadFolder() {
        if(Auth::user()->is_admin) {
            return $this->redirect('/documentation-maps/upload', navigate: true);
        }
        else {
            return $this->redirect('/documentation-maps', navigate: true);
        }
    }

}
