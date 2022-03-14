<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Register extends Component
{
    public function register()
    {
        dd('登録テスト');
    }

    public function render()
    {
        return view('livewire.register');
    }
}
