<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\ContaReceber;
use App\Models\MovCaixa;
use App\Models\MovVenda;
use App\Models\MovVendaIten;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PDVController extends Controller
{
    public function index()
    {
        // Carrega a view do PDV com os produtos e resumo da sessão
        $products = session('products', []);
        $summary = $this->calculateSummary($products);

        return view('sales.sales', compact('products', 'summary'));
    }

    public function addProduct(Request $request)
    {
        try {
            $products = session('products', []);
            $code = $request->input('code');
            $quantity = $request->input('quantity', 1);

            // Busca o produto no banco de dados
            $produto = Product::find($code);

            if (!$produto) {
                return response()->json(['success' => false, 'message' => 'Produto não encontrado.']);
            }

            // Calcula o total do produto
            $unitPrice = $produto->preco_venda;
            $totalPrice = $unitPrice * $quantity;

            // Adiciona ou atualiza o produto na sessão
            $products[$code] = [
                'code' => $produto->id,
                'nome' => $produto->nome,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ];

            session(['products' => $products]);

            // Recalcula o resumo
            $summary = $this->calculateSummary($products);

            return response()->json(['success' => true, 'product' => $products[$code], 'summary' => $summary]);
        } catch (\Exception $e) {
            Log::error("Erro ao adicionar produto: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Erro interno no servidor.'], 500);
        }
    }

    private function calculateSummary(array $products, $discount = 0, $surcharge = 0)
    {
        $subtotal = array_sum(array_column($products, 'total_price'));

        // Aplica descontos e acréscimos
        $total = max(0, $subtotal - $discount + $surcharge);

        return [
            'items' => count($products),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'surcharge' => $surcharge,
            'total' => $total,
        ];
    }

    public function cancelSale(Request $request)
    {
        return view('sales.sales');
    }

    public function finalizeSale(Request $request)
    {
        $products = session('products', []);
        $payments = $request->input('payments', []);
        $cliente_id = $request->input('cliente_id');
        $discountAmount = $request->input('discountAmount', 0);
        $discountType = $request->input('discountType', 'desconto');

        if (empty($products)) {
            return response()->json(['success' => false, 'message' => 'Adicione produtos à venda.']);
        }

        // Obter o caixa aberto
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if (!$caixaAberto) {
            return response()->json(['success' => false, 'message' => 'Nenhum caixa aberto encontrado.']);
        }

        // Verifica se há estoque suficiente para cada produto
        foreach ($products as $code => $product) {
            $produto = Product::find($code); // Use 'id' diretamente
            if (!$produto) {
                return response()->json(['success' => false, 'message' => "Produto com ID {$code} não encontrado."], 404);
            }
            if ($produto->estoque < $product['quantity']) {
                return response()->json(['success' => false, 'message' => "Estoque insuficiente para o produto {$produto->nome}."], 400);
            }
        }

        // Calcula o resumo da venda
        $summary = $this->calculateSummary($products, $discountType === 'desconto' ? $discountAmount : 0, $discountType === 'acrescimo' ? $discountAmount : 0);
        $totalPago = array_sum(array_column($payments, 'amount'));

        if ($totalPago < $summary['total']) {
            return response()->json(['success' => false, 'message' => 'O total pago é insuficiente.']);
        }

        DB::beginTransaction();

        try {
            // Cria a venda
            $venda = MovVenda::create([
                'id_usuario' => Auth::id(),
                'id_empresa' => Auth::user()->id_empresa,
                'id_cliente' => $cliente_id,
                'id_caixa' => $caixaAberto->id, // Associar a venda ao caixa
                'data_venda' => now(),
                'vl_total' => $summary['subtotal'],
                'vl_desconto' => $discountType === 'desconto' ? $discountAmount : 0,
                'vl_liquido' => $summary['total'],
                'status' => 'Finalizada',
            ]);

            // Verificar se o ID da venda foi criado corretamente
            if (!$venda->id) {
                throw new Exception('Falha ao criar a venda.');
            }

            // Adiciona os itens da venda
            $sequencia = 1;
            foreach ($products as $product) {
                $produto = Product::find($product['code']); // Use 'id' diretamente

                if (!$produto) {
                    throw new Exception("Produto com ID {$product['code']} não encontrado.");
                }

                MovVendaIten::create([
                    'id_mov_venda' => $venda->id,
                    'sequencia' => $sequencia++,
                    'quantidade' => $product['quantity'],
                    'vl_unitario' => $product['unit_price'],
                    'vl_total' => $product['total_price'],
                    'vl_liquido' => $product['total_price'], // Ajuste conforme a lógica de descontos
                    'product_id' => $produto->id,
                    'id_usuario' => Auth::id(),
                    'id_empresa' => Auth::user()->id_empresa,
                    'id_cliente' => $cliente_id,
                ]);

                // Atualiza o estoque do produto
                $produto->estoque -= $product['quantity'];
                $produto->save();
            }

            // Cria a movimentação do caixa associando a venda
            $movCaixa = MovCaixa::create([
                'id_caixa' => $caixaAberto->id,
                'id_empresa' => Auth::user()->id_empresa,
                'id_usuario' => Auth::id(),
                'id_movimento' => $venda->id,
                'tipo_movimentacao' => 'Venda',
                'descricao' => 'Venda realizada',
                'valor' => $summary['total'],
                'data_movimentacao' => now(),
            ]);

            // Atualiza o saldo atual do caixa
            $caixaAberto->saldo_atual += $summary['total'];
            $caixaAberto->save();

            // (Opcional) Processa os pagamentos
            foreach ($payments as $payment) {
                // Implemente a lógica de processamento de pagamentos conforme necessário
            }

            DB::commit();

            // Limpa a sessão
            session()->forget(['products', 'payments']);

            return response()->json(['success' => true, 'message' => 'Venda finalizada com sucesso.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao finalizar venda: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao finalizar venda: ' . $e->getMessage()]);
        }
    }
}
