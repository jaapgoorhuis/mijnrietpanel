<?php

namespace App\Livewire\Statistics;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;


class ExpandedStatistics extends Component
{
    use WithFileUploads;

    public $company;
    public $companyId;

    public $startYear;

    public $currentYear;

    public $numberOfYears;

    public $years = [];

    public function mount($id) {
        $this->companyId = $id;
    }
    public function render()
    {
        $this->company = Company::where('id', $this->companyId)->first();

        $this->startYear = $this->company->created_at->format('Y');

        $this->currentYear = date('Y');

        $this->numberOfYears = $this->currentYear - $this->startYear +1;

        for ($i = 0; $i < $this->numberOfYears; $i++) {
            array_push($this->years, $this->startYear++);
        }


        return view('livewire.statistics.expandedStatistics');

    }
}
