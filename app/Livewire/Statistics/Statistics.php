<?php

namespace App\Livewire\Statistics;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;


class Statistics extends Component
{
    use WithFileUploads;

    public $companys;

    public function render()
    {
        $this->companys = Company::get();

            return view('livewire.statistics.statistics');

    }

    public function expandedCompanyStats($id) {
        return $this->redirect('/statistics/'.$id, navigate: true);
    }

}
