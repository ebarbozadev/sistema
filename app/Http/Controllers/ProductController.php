<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function searchProductByCode($code)
    {
        try {
            // Verifica se o usuário está autenticado
            if (!Auth::check()) {
                return response()->json(['message' => 'Usuário não autenticado.'], 401);
            }

            // Obtém o ID da empresa do usuário autenticado
            $idEmpresa = Auth::user()->id_empresa;

            // Busca o produto pelo código e pela empresa
            $product = Product::where('id', $code)
                ->where('id_empresa', $idEmpresa)
                ->first(['id', 'nome', 'preco_venda']); // Seleciona apenas os campos necessários

            // Verifica se o produto foi encontrado
            if ($product) {
                return response()->json($product);
            } else {
                return response()->json(['message' => 'Produto não encontrado.'], 404);
            }
        } catch (\Exception $e) {
            // Registra o erro no log
            Log::error("Erro ao buscar produto por código: {$e->getMessage()}");

            return response()->json(['message' => 'Erro interno no servidor.'], 500);
        }
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

    public function searchProducts(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'Usuário não autenticado.'], 401);
            }

            $idEmpresa = Auth::user()->id_empresa;
            $query = $request->input('query');

            Log::info("Busca de produtos iniciada. Empresa: {$idEmpresa}, Query: '{$query}'");

            if (!$query) {
                Log::info("Nenhum termo de busca fornecido.");
                return response()->json([], 200);
            }

            $products = Product::where('id_empresa', $idEmpresa)
                ->where(function ($q) use ($query) {
                    $q->where('nome', 'LIKE', "%{$query}%")
                        ->orWhere('id', $query);
                })
                ->limit(10)
                ->get();

            Log::info("Produtos encontrados: ", $products->toArray());

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar produtos: {$e->getMessage()}");

            return response()->json(['message' => 'Erro interno no servidor.'], 500);
        }
    }
}
