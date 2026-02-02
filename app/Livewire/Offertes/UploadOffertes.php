<?php

namespace App\Livewire\Offertes;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadOffertes extends Component
{
    use WithFileUploads;

    public $orderForm;


    public function mount() {
        if(Auth::user()->is_admin) {
            return view('livewire.offertes.uploadOfferte');
        } else {
            session()->flash('error','U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }
    }
    public function render()
    {
        return view('livewire.offertes.uploadOfferte');
    }

    protected $rules = [
        'orderForm' => 'required|file|mimes:pdf|max:2048',
    ];

    public function messages(): array
    {
        return [
            'orderForm.required' => __('messages.Het is verplicht om een bestand te uploaden.'),
            'orderForm.mimes' => __('messages.Het bestand moet een pdf bestand zijn.'),
            'orderForm.max' => __('messages.Het bestand mag niet groter dan 2MB zijn.'),
        ];
    }

    public function uploadOrderForm() {
        $this->validate();
        Storage::delete('rietpanel-order-formulier.pdf');
        $this->orderForm->storeAs(path: 'uploads', name: 'rietpanel-order-formulier.pdf', options: 'public');

        session()->flash('success',__('messages.Het nieuwe inmeet formulier is geupload'));
        return $this->redirect('/offertes', navigate: true);
    }

    public function cancelUploadOrderForm() {
        return $this->redirect('/offertes', navigate: true);
    }
}
