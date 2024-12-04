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
                'DATA_VENDA' => now(),
                'VL_TOTAL' => $summary['subtotal'],
                'VL_DESCONTO' => $discountType === 'desconto' ? $discountAmount : 0,
                'VL_LIQUIDO' => $summary['total'],
                'STATUS' => 'Finalizada',
            ]);

            // Adiciona os itens da venda
            $sequencia = 1;
            foreach ($products as $product) {
                MovVendaIten::create([
                    'ID_MOV_VENDA' => $venda->ID, // Alterado de $venda->id para $venda->ID
                    'SEQUENCIA' => $sequencia++,
                    'QUANTIDADE' => $product['quantity'],
                    'VL_UNITARIO' => $product['unit_price'],
                    'VL_TOTAL' => $product['total_price'],
                    'VL_LIQUIDO' => $product['total_price'],
                    'id_usuario' => Auth::id(),
                    'id_empresa' => Auth::user()->id_empresa,
                    'id_cliente' => $cliente_id, // Adicione esta linha
                ]);

                // Atualiza o estoque do produto
                $produto = Product::find($product['code']);
                $produto->estoque -= $product['quantity'];
                $produto->save();
            }

            // Registra as movimentações de caixa
            $caixa = Caixa::where('ID_EMPRESA', Auth::user()->id_empresa)
                ->where('STATUS', 'Aberto')
                ->first();

            if (!$caixa) {
                throw new Exception('Caixa não está aberto.');
            }

            foreach ($payments as $payment) {
                MovCaixa::create([
                    'ID_CAIXA' => $caixa->id, // Alterado de $caixa->ID para $caixa->id
                    'ID_EMPRESA' => Auth::user()->id_empresa,
                    'ID_USUARIO' => Auth::id(),
                    'ID_MOVIMENTO' => $venda->ID,
                    'TIPO_MOVIMENTACAO' => 'Venda',
                    'DESCRICAO' => 'Venda ID ' . $venda->ID,
                    'VALOR' => $payment['amount'],
                    'DATA_MOVIMENTACAO' => now(),
                ]);

                // Atualiza o saldo do caixa
                $caixa->SALDO_ATUAL += $payment['amount'];
            }

            $caixa->save();

            // Cria o registro em contas a receber
            ContaReceber::create([
                'id_empresa' => Auth::user()->id_empresa,
                'id_usuario' => Auth::id(),
                'id_cliente' => $cliente_id,
                'valor' => $summary['total'] - $totalPago, // Valor restante a receber
                'data_vencimento' => now(), // Ou especifique uma data futura
                'parcela' => '1/1', // Ajuste conforme necessário
                'status' => ($summary['total'] - $totalPago) > 0 ? 'Pendente' : 'Pago',
                'descricao' => 'Venda ID ' . $venda->id,
            ]);

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

    // Outros métodos...
}