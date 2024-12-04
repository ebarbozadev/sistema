<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        // Customiza o comportamento do browser
        $clients = Cliente::select('id', 'nome', 'email', 'tipo_pessoa', 'documento', 'telefone_residencial', 'endereco_residencial', 'ativo')->paginate(10);

        return view('vendor.voyager.clients.index', compact('clients'));
    }

    public function searchClients(Request $request)
    {
        try {
            // If authentication is required
            if (!Auth::check()) {
                return response()->json(['message' => 'Usuário não autenticado.'], 401);
            }

            $query = $request->input('query');

            if (!$query) {
                return response()->json([], 200);
            }

            // Fetch clients matching the query
            $clients = Cliente::where('nome', 'LIKE', "%{$query}%")
                ->orWhere('id', $query)
                ->limit(10)
                ->get(['id', 'nome']);

            return response()->json($clients);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar clientes: {$e->getMessage()}");

            return response()->json(['message' => 'Erro interno no servidor.'], 500);
        }
    }


    public function create()
    {
        $dataType = DataType::where('slug', 'clients')->firstOrFail();
        $dataTypeContent = new \App\Models\Cliente(); // Instância vazia do modelo Cliente
        $isModelTranslatable = is_bread_translatable($dataTypeContent); // Verifica se o modelo suporta tradução

        return view('vendor.voyager.clients.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    public function edit(Request $request, $id)
    {
        $dataType = DataType::where('slug', 'clients')->firstOrFail(); // Obtém o tipo de dados
        $dataTypeContent = Cliente::findOrFail($id); // Busca o cliente pelo ID
        $isModelTranslatable = is_bread_translatable($dataTypeContent); // Verifica se o modelo suporta tradução

        return view('vendor.voyager.clients.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255', // Adicione esta linha
            'email' => 'required|email|max:255',
            'tipo_pessoa' => 'required|string|max:1',
            'documento' => 'required|string|max:20',
            'data_nascimento' => 'nullable|date',
            'endereco_responsavel' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:20',
            'endereco_residencial' => 'nullable|string|max:255',
            'telefone_residencial' => 'nullable|string|max:20',
            'endereco_comercial' => 'nullable|string|max:255',
            'telefone_comercial' => 'nullable|string|max:20',
            'endereco_outros' => 'nullable|string|max:255',
            'telefone_outros' => 'nullable|string|max:20',
            'ativo' => 'required|boolean',
        ]);

        Cliente::create($validatedData);

        return redirect()->route('voyager.clients.index')->with('success', 'Cliente criado com sucesso!');
    }

    public function destroy($id)
    {
        try {
            // Busca o cliente pelo ID
            $client = Cliente::findOrFail($id);

            // Exclui o cliente
            $client->delete();

            // Redireciona com uma mensagem de sucesso
            return redirect()->route('voyager.clients.index')->with('success', 'Cliente excluído com sucesso!');
        } catch (\Exception $e) {
            // Retorna com uma mensagem de erro caso algo dê errado
            return redirect()->route('voyager.clients.index')->with('error', 'Erro ao excluir o cliente.');
        }
    }

    public function update(Request $request, $id)
    {
        // Validação dos dados recebidos
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255', // Adicione esta linha
            'email' => 'required|email|max:255',
            'tipo_pessoa' => 'nullable|in:f,j',
            'documento' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'telefone_residencial' => 'nullable|string|max:20',
            'endereco_residencial' => 'nullable|string|max:150',
            'telefone_comercial' => 'nullable|string|max:20',
            'endereco_comercial' => 'nullable|string|max:150',
            'telefone_outros' => 'nullable|string|max:20',
            'endereco_outros' => 'nullable|string|max:150',
            'ativo' => 'required|integer|in:0,1',
        ]);

        // Encontrar o cliente pelo ID
        $client = Cliente::findOrFail($id);

        // Atualizar os campos com os dados validados
        $client->update($validatedData);

        // Retornar uma resposta (redirecionar ou mensagem de sucesso)
        return redirect()->route('voyager.clients.index')->with('success', 'Cliente atualizado com sucesso!');
    }
}
