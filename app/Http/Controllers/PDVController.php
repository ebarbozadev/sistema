<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\MovCaixa;
use App\Models\MovVenda;
use App\Models\MovVendaIten;
use App\Models\Produto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $products = session('products', []);
        $code = $request->input('code');
        $quantity = $request->input('quantity', 1);

        // Busca o produto no banco de dados
        $produto = Produto::find($code);

        if (!$produto) {
            return response()->json(['success' => false, 'message' => 'Produto não encontrado.']);
        }

        // Calcula o total do produto
        $unitPrice = $produto->precoVenda;
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
    }

    private function calculateSummary($products)
    {
        $items = count($products);
        $subtotal = array_sum(array_column($products, 'total_price'));

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal, // Ajustar se houver descontos/acréscimos
        ];
    }

    public function finalizeSale(Request $request)
    {
        $products = session('products', []);
        $payments = session('payments', []);
        $cliente_id = $request->input('cliente_id');

        if (empty($products)) {
            return redirect()->back()->with('error', 'Adicione produtos à venda.');
        }

        $summary = $this->calculateSummary($products);
        $totalPago = array_sum(array_column($payments, 'amount'));

        if ($totalPago < $summary['total']) {
            return redirect()->back()->with('error', 'O total pago é insuficiente.');
        }

        DB::beginTransaction();

        try {
            // Cria a venda
            $venda = MovVenda::create([
                'ID_USUARIO' => Auth::id(),
                'ID_EMPRESA' => Auth::user()->empresa_id,
                'ID_CLIENTE' => $cliente_id,
                'DT_VENDA' => now(),
                'VL_TOTAL' => $summary['subtotal'],
                'VL_DESCONTO' => 0, // Ajustar se houver
                'VL_LIQUIDO' => $summary['total'],
                'STATUS' => 'Finalizada',
            ]);

            // Adiciona os itens da venda
            foreach ($products as $product) {
                MovVendaIten::create([
                    'ID_MOVVENDA' => $venda->ID,
                    'SEQUENCIA' => 1, // Incrementar conforme necessário
                    'QUANTIDADE' => $product['quantity'],
                    'VL_UNITARIO' => $product['unit_price'],
                    'VL_TOTAL' => $product['total_price'],
                    'VL_LIQUIDO' => $product['total_price'],
                ]);

                // Atualiza o estoque do produto
                $produto = Produto::find($product['code']);
                $produto->estoque -= $product['quantity'];
                $produto->save();
            }

            // Registra as movimentações de caixa
            $caixa = Caixa::where('ID_EMPRESA', Auth::user()->empresa_id)
                ->where('STATUS', 'Aberto')
                ->first();

            if (!$caixa) {
                throw new Exception('Caixa não está aberto.');
            }

            foreach ($payments as $payment) {
                MovCaixa::create([
                    'ID_CAIXA' => $caixa->ID,
                    'ID_EMPRESA' => Auth::user()->empresa_id,
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

            DB::commit();

            // Limpa a sessão
            session()->forget(['products', 'payments']);

            return redirect()->route('pdv')->with('success', 'Venda finalizada com sucesso.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao finalizar venda: ' . $e->getMessage());
        }
    }


    // Outros métodos...
}
