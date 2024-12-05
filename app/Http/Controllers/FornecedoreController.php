<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\Fornecedore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FornecedoreController extends Controller
{
    /**
     * Busca fornecedores com base em uma query e filtra por id_empresa do usuário autenticado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchSuppliers(Request $request)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            Log::warning('Tentativa de busca de fornecedores sem autenticação.');
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        // Validação da requisição
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $query = $request->input('query', '');

        // Obtém o id_empresa do usuário autenticado
        $id_empresa = Auth::user()->id_empresa;

        // Log das informações recebidas
        Log::info("Buscando fornecedores para id_empresa: {$id_empresa} com query: {$query}");

        // Busca fornecedores cujo nome contém a query e pertencem à id_empresa
        try {
            $suppliers = Fornecedore::where('id_empresa', $id_empresa)
                ->where('nome', 'LIKE', "%{$query}%")
                ->limit(10) // Limita os resultados para performance
                ->get();

            Log::info("Fornecedores encontrados: " . $suppliers->count());

            return response()->json($suppliers);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar fornecedores: " . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar fornecedores'], 500);
        }
    }
}
