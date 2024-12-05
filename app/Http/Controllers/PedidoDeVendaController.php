<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\MovCaixa;
use App\Models\MovVenda;
use App\Models\MovVendaIten;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoDeVendaController extends Controller
{
    public function index()
    {
        // Verifica o caixa
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if ($caixaAberto) {
            $dataAbertura = Carbon::parse($caixaAberto->data_abertura);
            $hoje = now()->timezone('America/Sao_Paulo');

            if ($dataAbertura->toDateString() != $hoje->toDateString()) {
                return redirect()->route('caixa.fecharAnterior');
            }
        } else {
            // Não há caixa aberto, redireciona para abrir um novo caixa
            return redirect()->route('caixa.index')->with('error', 'Não há caixa aberto. Abra um caixa para continuar.');
        }

        // Produtos e resumo já definidos
        $products = session()->get('products', []);
        $summary = [
            'items' => count($products),
            'subtotal' => array_sum(array_column($products, 'total_price')),
            'discount' => 0.00,
            'total' => array_sum(array_column($products, 'total_price')),
        ];

        // Pagamentos registrados na sessão
        $payments = session()->get('payments', []); // Carrega pagamentos ou inicializa como array vazio

        return view('sales.sales', compact('products', 'summary', 'payments'));
    }

    public function updateProduct(Request $request, $index)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        $products = session()->get('products', []);
        $payments = session()->get('payments', []);

        if (isset($products[$index])) {
            $products[$index]['quantity'] = $request->quantity;
            $products[$index]['total_price'] = $request->quantity * $products[$index]['unit_price'];

            session()->put('products', $products);

            $summary = [
                'items' => count($products),
                'subtotal' => array_sum(array_column($products, 'total_price')),
                'discount' => 0.00,
                'total' => array_sum(array_column($products, 'total_price')),
            ];

            // Calcula o total pago até o momento
            $totalPaid = array_sum(array_column($payments, 'amount'));
            // Calcula o restante
            $remaining = $summary['total'] - $totalPaid;

            return response()->json([
                'success' => true,
                'product' => $products[$index],
                'summary' => $summary,
                'remaining' => $remaining,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Produto não encontrado.'], 404);
    }

    public function removeProduct($index)
    {
        // Obter os produtos da sessão
        $products = session()->get('products', []);

        // Verificar se o índice existe
        if (isset($products[$index])) {
            // Remover o produto
            unset($products[$index]);

            // Reindexar o array
            session()->put('products', array_values($products));
        }

        return redirect('/admin/pedido-de-venda')->with('success', 'Produto removido com sucesso!');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'method' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->input('amount');
        $payments = session()->get('payments', []);
        $summary = session()->get('summary', []);

        $totalOrder = $summary['total'] ?? 0;
        $totalPaid = array_sum(array_column($payments, 'amount'));

        // Validar se o valor excede o total
        if ($totalPaid + $amount > $totalOrder) {
            return response()->json([
                'success' => false,
                'message' => 'O valor total pago não pode exceder o valor total da venda.',
            ]);
        }

        // Adicionar o pagamento
        $payments[] = ['method' => $request->input('method'), 'amount' => $amount];
        session()->put('payments', $payments);

        // Retornar o resumo atualizado
        $summary['paid'] = $totalPaid + $amount;
        $summary['remaining'] = $totalOrder - $summary['paid'];

        return response()->json(['success' => true, 'summary' => $summary]);
    }

    private function updateSummary()
    {
        // Lógica para calcular e atualizar o resumo de pagamentos
        // Exemplo fictício
        return [
            'items' => 5, // Número de itens no pedido
            'subtotal' => 500.00, // Subtotal calculado
            'total' => 480.00, // Total considerando descontos ou acréscimos
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



    private function calculateSummary($products, $desconto, $acrescimo)
    {
        $items = count($products);
        $subtotal = 0;

        foreach ($products as $product) {
            $subtotal += $product['total_price'];
        }

        $total = $subtotal - $desconto + $acrescimo;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id', // Use 'id' em vez de 'code'
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $productData = Product::find($request->id); // Use 'id'

        if (!$productData) {
            return response()->json(['success' => false, 'message' => 'Produto não encontrado.'], 404);
        }

        $products = session()->get('products', []);
        $existingProductIndex = array_search($productData->id, array_column($products, 'code'));

        if ($existingProductIndex !== false) {
            // Incrementa a quantidade do produto existente
            $products[$existingProductIndex]['quantity'] += $request->quantity;
            $products[$existingProductIndex]['total_price'] =
                $products[$existingProductIndex]['quantity'] * $products[$existingProductIndex]['unit_price'];
        } else {
            // Adiciona um novo produto
            $products[] = [
                'code' => $productData->id, // 'code' representa 'id' aqui
                'nome' => $productData->nome,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total_price' => $request->quantity * $request->unit_price,
            ];
        }

        session()->put('products', $products);

        $summary = [
            'items' => count($products),
            'subtotal' => array_sum(array_column($products, 'total_price')),
            'discount' => 0.00,
            'total' => array_sum(array_column($products, 'total_price')),
        ];

        return response()->json([
            'success' => true,
            'product' => $products[$existingProductIndex ?? array_key_last($products)],
            'summary' => $summary,
        ]);
    }
}
