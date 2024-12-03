<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContasReceberController extends Controller
{
    public function index()
    {
        // Busque os dados das contas a receber no banco
        $contasReceber = ReceberConta::all(); // Atualize conforme necessário

        return view('voyager::receber-contas.index', compact('contasReceber'));
    }
}
