<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\Fornecedore;
use Illuminate\Http\Request;

class FornecedoreController extends Controller
{
    public function searchSuppliers(Request $request)
    {
        $query = $request->input('query');

        $fornecedores = Fornecedore::where('nome', 'LIKE', '%' . $query . '%')
            ->orWhere('id', $query)
            ->limit(10)
            ->get();

        return response()->json($fornecedores);
    }

    // Outros métodos para criar, editar, excluir fornecedores
}
