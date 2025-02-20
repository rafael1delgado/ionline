<?php

namespace App\Http\Livewire\ProfAgenda;

use Livewire\Component;
use App\Models\ClCommune;
use App\User;

class EmployeeData extends Component
{
    public $user_id = 0;
    public $dv;
    public $email;
    public $message;

    // public function mount(){
    //     dd($this->user_id);
    // }

    protected $listeners = ['loadUserData' => 'loadUserData'];

    public function loadUserData(User $User){
        $this->user_id = $User->id;
        $this->dv = $User->dv;
        $this->render();
    }

    public function render()
    {
        $communes = ClCommune::orderBy('name', 'ASC')->get();
        $user = new User();

        $this->message = "";
        if ($this->user_id > 3000000) {
          $user = User::find($this->user_id);
          $this->emit('renderFromEmployeeData');
          if ($user) {
            $this->email = $user->email;
          }else{
            // validación correo
            if ($this->email != null) {
              $user = User::where('email',$this->email)->first();
              if ($user != null) {
                $this->message = "No es posible utilizar el coreo " . $this->email . ", ya está siendo utilizado por " . $user->getFullNameUpperAttribute();
              }
            }
          }
        }

        return view('livewire.prof-agenda.employee-data',compact('user','communes'));
    }
}
