<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoDeVendaController extends Controller
{
    public function index()
    {
        // Verifica o caixa
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if ($caixaAberto) {
            $dataAbertura = $caixaAberto->data_abertura;
            $hoje = now()->format('Y-m-d');

            if ($dataAbertura != $hoje) {
                // Redireciona para a tela de fechamento do caixa
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

    public function addProduct(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
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
