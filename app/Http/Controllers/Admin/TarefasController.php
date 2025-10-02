<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class TarefasController extends Controller
{
    public function tarefas()
    {
        return view('admin.comercial.quadro-tarefas');
    }
}
