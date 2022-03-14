<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Register extends Component
{
    public $name;
    public $email;
    public $password;

    public function register()
    {
        dd($this);
    }

    public function render()
    {
        return view('livewire.register');
    }
}
