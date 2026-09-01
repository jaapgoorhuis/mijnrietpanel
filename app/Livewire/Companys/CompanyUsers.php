<?php

namespace App\Livewire\Companys;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use function Spatie\LaravelPdf\Support\pdf;

class CompanyUsers extends Component
{
    public $users;

    public $company_id;

    public $company;
    public function mount($id) {
        $this->company_id = $id;

    }
    public function render()
    {
        if(Auth::user()->is_admin) {
            $this->users = User::where('bedrijf_id', $this->company_id)->where('is_removed', '0')->get();
            $this->company = Company::where('id', $this->company_id)->first();

            $lastActivity = DB::table('sessions')
                ->whereIn('user_id', $this->users->pluck('id'))
                ->selectRaw('user_id, MAX(last_activity) as last_activity')
                ->groupBy('user_id')
                ->pluck('last_activity', 'user_id');

            $this->users->each(function ($user) use ($lastActivity) {
                $user->last_activity_at = $lastActivity->has($user->id)
                    ? Carbon::createFromTimestamp($lastActivity->get($user->id), config('app.timezone'))
                    : null;
            });

            return view('livewire.companys.companyUsers');
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function newUser() {
        return $this->redirect('/companys/'.$this->company_id.'/users/create', navigate: true);
    }

    public function removeUser($id){
        return $this->redirect('/companys/'.$this->company_id.'/users/remove/'.$id, navigate: true);
    }

    public function editUser($id) {
        return $this->redirect('/companys/'.$this->company_id.'/users/edit/'.$id, navigate: true);
    }
}
