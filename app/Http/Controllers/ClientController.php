<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        // Customiza o comportamento do browser
        $clients = Client::select('id', 'name', 'email', 'tp_people', 'document', 'telephone_res', 'telephone_res_res', 'status')->paginate(10);

        return view('vendor.voyager.clients.index', compact('clients'));
    }

    public function searchClients(Request $request)
    {
        $query = $request->get('query');

        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('id', $query) // Permite buscar diretamente pelo ID exato
            ->take(10)
            ->get(['id', 'name']); // Retorna apenas os campos necessários

        return response()->json($clients);
    }

    public function create(Request $request)
    {
        // Customiza o comportamento do formulário de criação
        return view('voyager.clients.edit-add');
    }

    public function edit(Request $request, $id)
    {
        // Customiza o comportamento do formulário de edição
        $client = Client::findOrFail($id);
        return view('voyager.clients.edit-add', compact('client'));
    }


    public function update(Request $request, $id)
    {
        // Validação dos dados recebidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tp_people' => 'nullable|in:f,j',
            'document' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'telephone_res' => 'nullable|string|max:20',
            'telephone_res_res' => 'nullable|string|max:150',
            'telephone_com' => 'nullable|string|max:20',
            'telephone_com_res' => 'nullable|string|max:150',
            'telephone_other' => 'nullable|string|max:20',
            'telephone_other_res' => 'nullable|string|max:150',
            'status' => 'required|integer|in:0,1',
        ]);

        // Encontrar o cliente pelo ID
        $client = Client::findOrFail($id);

        // Atualizar os campos com os dados validados
        $client->update($validatedData);

        // Retornar uma resposta (redirecionar ou mensagem de sucesso)
        return redirect()
            ->route('voyager.clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }
}
