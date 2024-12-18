<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\MovCompra;
use App\Models\MovCompraItem;
use App\Models\Product;
use App\Models\Fornecedor;
use App\Models\MovCompraIten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoDeCompraController extends Controller
{
    /**
     * Exibe a página principal de Pedido de Compra.
     */
    public function index()
    {
        // Limpa os produtos e pagamentos da sessão
        session()->forget(['products', 'payments']);

        // Inicia produtos e resumo vazios
        $products = [];
        $summary = $this->calculateSummary($products);

        return view('pedidoDeCompra.sales', compact('products', 'summary'));
    }

    /**
     * Calcula o resumo da compra.
     *
     * @param array $products
     * @param float $discount
     * @param float $surcharge
     * @return array
     */
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

    /**
     * Finaliza a compra.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizePurchase(Request $request)
    {
        $products = session('products', []);
        $payments = $request->input('payments', []);
        $fornecedor_id = $request->input('fornecedor_id');
        $discountAmount = $request->input('discountAmount', 0);
        $discountType = $request->input('discountType', 'desconto');

        if (empty($products)) {
            return response()->json(['success' => false, 'message' => 'Adicione produtos à compra.']);
        }

        // Calcula o resumo da compra
        $summary = $this->calculateSummary(
            $products,
            $discountType === 'desconto' ? $discountAmount : 0,
            $discountType === 'acrescimo' ? $discountAmount : 0
        );
        $totalPago = array_sum(array_column($payments, 'amount'));

        if ($totalPago < $summary['total']) {
            return response()->json(['success' => false, 'message' => 'O total pago é insuficiente.']);
        }

        DB::beginTransaction();

        try {
            // Cria a compra
            $compra = MovCompra::create([
                'id_usuario' => Auth::id(),
                'id_empresa' => Auth::user()->id_empresa,
                'id_fornecedor' => $fornecedor_id,
                'data_compra' => now(),
                'vl_total' => $summary['subtotal'],
                'vl_desconto' => $discountType === 'desconto' ? $discountAmount : 0,
                'vl_liquido' => $summary['total'],
                'status' => 'Finalizada',
            ]);

            // Adiciona os itens da compra
            foreach ($products as $index => $product) {
                MovCompraIten::create([
                    'id_mov_compra' => $compra->id,
                    'sequencia' => $index + 1,
                    'quantidade' => $product['quantity'],
                    'vl_unitario' => $product['unit_price'],
                    'vl_total' => $product['total_price'],
                    'vl_liquido' => $product['total_price'],
                    'id_usuario' => Auth::id(),
                    'id_empresa' => Auth::user()->id_empresa,
                    'id_fornecedor' => $fornecedor_id,
                    'id_produto' => $product['code'], // Certifique-se de que 'code' é o 'id_produto'
                ]);

                // Atualiza o estoque do produto (incrementa)
                $produto = Product::find($product['code']);
                if ($produto) {
                    $produto->estoque += $product['quantity'];
                    $produto->save();
                } else {
                    throw new \Exception("Produto com código {$product['code']} não encontrado.");
                }
            }

            // Registrar os pagamentos, se necessário
            // Dependendo de como os pagamentos estão sendo gerenciados, pode ser necessário criar registros na tabela de pagamentos

            DB::commit();

            // Limpa a sessão
            session()->forget(['products', 'payments']);

            return response()->json(['success' => true, 'message' => 'Compra finalizada com sucesso.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao finalizar compra: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao finalizar compra: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualiza a quantidade de um produto.
     *
     * @param Request $request
     * @param int $index
     * @return \Illuminate\Http\JsonResponse
     */
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

            $summary = $this->calculateSummary($products);
            $totalPaid = array_sum(array_column($payments, 'amount'));
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

    /**
     * Remove um produto da compra.
     *
     * @param int $index
     * @return \Illuminate\Http\RedirectResponse
     */
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

        return redirect('/admin/pdc/pedido-de-compra')->with('success', 'Produto removido com sucesso!');
    }

    /**
     * Processa um pagamento.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'method' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->input('amount');
        $payments = session()->get('payments', []);
        $summary = $this->calculateSummary(session('products', []));
        $totalOrder = $summary['total'] ?? 0;
        $totalPaid = array_sum(array_column($payments, 'amount'));

        // Validar se o valor excede o total
        if ($totalPaid + $amount > $totalOrder) {
            return response()->json([
                'success' => false,
                'message' => 'O valor total pago não pode exceder o valor total da compra.',
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

    /**
     * Adiciona um produto à compra.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProduct(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:products,id', // Verifica se o produto existe
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $productData = Product::find($request->code);

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
                'code' => $productData->id,
                'nome' => $productData->nome,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total_price' => $request->quantity * $request->unit_price,
            ];
        }

        session()->put('products', $products);

        $summary = $this->calculateSummary($products);

        return response()->json([
            'success' => true,
            'product' => $products[$existingProductIndex ?? array_key_last($products)],
            'summary' => $summary,
        ]);
    }

    /**
     * Atualiza o resumo após alterações na compra.
     *
     * @return array
     */
    private function updateSummary()
    {
        // Implementação personalizada, conforme a lógica do seu sistema
        return [
            'items' => 5, // Número de itens no pedido
            'subtotal' => 500.00, // Subtotal calculado
            'total' => 480.00, // Total considerando descontos ou acréscimos
        ];
    }
}
