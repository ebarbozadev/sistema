<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');

        // Validação básica
        if (!$query) {
            return response()->json([], 200);
        }

        // Busca produtos pelo nome ou código que correspondem à consulta
        $products = Product::where('nome', 'LIKE', "%{$query}%")
            ->orWhere('id', $query)
            ->limit(10)
            ->get(['id', 'nome', 'precoVenda']); // Seleciona apenas os campos necessários

        return response()->json($products);
    }

    public function removeImage(Request $request)
    {
        $imagem = $request->input('id');
        if (Storage::exists($imagem)) {
            Storage::delete($imagem);
            return response()->json(['message' => 'Imagem removida com sucesso!']);
        }
        return response()->json(['message' => 'Erro ao remover imagem.'], 400);
    }

    public function searchProductByCode($code)
    {
        $product = Product::where('id', $code)->first(['id', 'nome', 'precoVenda']);

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }
    }
}
