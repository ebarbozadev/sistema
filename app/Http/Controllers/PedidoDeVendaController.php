<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PedidoDeVendaController extends Controller
{
    public function index()
    {

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

            return response()->json([
                'success' => true,
                'product' => $products[$index],
                'summary' => $summary,
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

        return redirect()->route('pdv.index')->with('success', 'Produto removido com sucesso!');
    }

    public function processPayment(Request $request)
    {
        $data = $request->all();

        if ($data['method'] === 'cartao') {
            // Integração com API de pagamento
            $cardDetails = $data['card'];
            $response = $this->processCardPayment($cardDetails, $data['amount']);

            if (!$response['success']) {
                return response()->json(['success' => false, 'message' => $response['message']]);
            }
        }

        // Registrar o pagamento no banco de dados
        Payment::create([
            'method' => $data['method'],
            'amount' => $data['amount'],
            // Outros dados podem ser adicionados se necessário
        ]);

        // Atualizar o resumo de pagamentos
        $summary = $this->updateSummary(); // Certifique-se de implementar este método corretamente

        return response()->json(['success' => true, 'summary' => $summary]);
    }

    private function processCardPayment($cardDetails, $amount)
    {
        // Exemplo fictício de integração com API de pagamento
        $apiResponse = PaymentAPI::charge([
            'card_number' => $cardDetails['number'],
            'card_holder' => $cardDetails['holder'],
            'card_expiry' => $cardDetails['expiry'],
            'card_cvv' => $cardDetails['cvv'],
            'amount' => $amount,
        ]);

        return [
            'success' => $apiResponse->isSuccess(),
            'message' => $apiResponse->getMessage(),
        ];
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
            $products[$existingProductIndex]['quantity'] += $request->quantity;
            $products[$existingProductIndex]['total_price'] = $products[$existingProductIndex]['quantity'] * $products[$existingProductIndex]['unit_price'];
        } else {
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
