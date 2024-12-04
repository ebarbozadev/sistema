<?php

namespace App\Http\Controllers;

use App\Models\ContaReceber;
use Illuminate\Http\Request;

class ContasReceberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['fornecedor', 'status', 'data_inicio', 'data_fim']);

        $contas = ContaReceber::query()
            ->when($filters['fornecedor'] ?? null, fn($query, $fornecedor) => $query->where('ID_FORNECEDOR', $fornecedor))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('STATUS', $status))
            ->when($filters['data_inicio'] ?? null, fn($query, $dataInicio) => $query->where('DATA_VENCIMENTO', '>=', $dataInicio))
            ->when($filters['data_fim'] ?? null, fn($query, $dataFim) => $query->where('DATA_VENCIMENTO', '<=', $dataFim))
            ->orderBy('DATA_VENCIMENTO', 'asc')
            ->paginate(10);

        return view('contas_receber.index', compact('contas', 'filters'));
    }

    public function create()
    {
        return view('contas_receber.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_EMPRESA' => 'required|integer',
            'ID_USUARIO' => 'required|integer',
            'ID_FORNECEDOR' => 'required|integer',
            'DESCRICAO' => 'required|string|max:255',
            'VALOR' => 'required|numeric',
            'DATA_VENCIMENTO' => 'required|date',
            'PARCELA' => 'required|string|max:20',
            'STATUS' => 'required|in:Pendente,Pago,Atrasado',
        ]);

        $validated['ID_EMPRESA'] = auth()->user()->id_empresa ?? $request->input('ID_EMPRESA');

        ContaReceber::create($validated);

        return redirect()->route('contas_receber.index')->with('success', 'Conta a receber criada com sucesso!');
    }

    public function edit(ContaReceber $conta)
    {
        return view('contas_receber.form', compact('conta'));
    }

    public function update(Request $request, ContaReceber $conta)
    {
        $validated = $request->validate([
            'ID_FORNECEDOR' => 'required|integer',
            'DESCRICAO' => 'required|string|max:255',
            'VALOR' => 'required|numeric',
            'DATA_VENCIMENTO' => 'required|date',
            'PARCELA' => 'required|string|max:20',
            'STATUS' => 'required|in:Pendente,Pago,Atrasado',
        ]);

        $conta->update($validated);

        return redirect()->route('contas_receber.index')->with('success', 'Conta a receber atualizada com sucesso!');
    }

    public function destroy(ContaReceber $conta)
    {
        $conta->delete();

        return redirect()->route('contas_receber.index')->with('success', 'Conta a receber excluída com sucesso!');
    }
}
