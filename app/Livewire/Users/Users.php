<?php

namespace App\Livewire\Users;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class Users extends Component
{
    public $users;
    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->users = User::get();
            return view('livewire.users.users');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function editUser($id) {
        return $this->redirect('/user/edit/'.$id, navigate: true);
    }
}
