<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ColetaController extends Controller
{
    public $request, $user;
    
    public function __constuct(Request $request, User $user){
        $this->request = $request;
        $this->user = $user;
    }

    public function coleta()
    {        
        return view('admin.comercial.coleta-vendedor');
    }
}
