<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function searchProducts(Request $request)
    {
        // Obter o id_empresa do usuário autenticado
        $idEmpresa = Auth::user()->id_empresa;

        // Buscar produtos pelo termo de descrição e id_empresa
        $products = Product::where('id_empresa', $idEmpresa)
            ->where('nome', 'like', '%' . $request->input('query') . '%')
            ->take(10) // Limitar os resultados para evitar sobrecarga
            ->get(['id', 'nome', 'precoVenda']); // Selecionar somente os campos necessários

        return response()->json($products);
    }

    public function searchProductByCode($code)
    {
        $product = Product::where('id', $code)->first(['id', 'nome', 'precoVenda']);

        if ($product) {
            return response()->json($product);
        }

        return response()->json(null, 404); // Produto não encontrado
    }
}
