<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovCaixa;
use App\Models\Caixa;
use Carbon\Carbon;

class CaixaController extends Controller
{
    public function index()
    {
        // Pegue os dados necessários para exibir no view
        $caixas = []; // Busque os dados reais do banco se necessário.

        return view('voyager::caixas.index', compact('caixas'));
    }


    public function info($id)
    {
        $movimentacao = MovCaixa::findOrFail($id);
        return view('caixa.info', compact('movimentacao'));
    }

    public function edit($id)
    {
        $movimentacao = MovCaixa::findOrFail($id);
        return view('caixa.edit', compact('movimentacao'));
    }


    public function storeMovimentacao(Request $request)
    {
        $request->validate([
            'TIPO_MOVIMENTACAO' => 'required|string|in:Venda,Sangria,Suprimento,Pagamento,Recebimento',
            'VALOR' => 'required|numeric|min:0.01',
            'DESCRICAO' => 'nullable|string|max:255',
        ]);

        $caixaAberto = Caixa::where('STATUS', 'Aberto')->first();

        if (!$caixaAberto) {
            return redirect()->route('caixa.index')->with('error', 'Nenhum caixa está aberto.');
        }

        MovCaixa::create([
            'ID_CAIXA' => $caixaAberto->ID,
            'ID_EMPRESA' => $caixaAberto->ID_EMPRESA,
            'ID_USUARIO' => auth()->id(),
            'TIPO_MOVIMENTACAO' => $request->TIPO_MOVIMENTACAO,
            'VALOR' => $request->VALOR,
            'DESCRICAO' => $request->DESCRICAO,
        ]);

        $caixaAberto->SALDO_ATUAL += $request->TIPO_MOVIMENTACAO === 'Venda' || $request->TIPO_MOVIMENTACAO === 'Recebimento'
            ? $request->VALOR
            : -$request->VALOR;

        $caixaAberto->save();

        return redirect()->route('caixa.index')->with('success', 'Movimentação registrada com sucesso.');
    }

    public function fecharCaixa(Request $request)
    {
        $caixaAberto = Caixa::where('STATUS', 'Aberto')->first();

        if (!$caixaAberto) {
            return redirect()->route('caixa.index')->with('error', 'Nenhum caixa está aberto.');
        }

        $caixaAberto->update([
            'STATUS' => 'Fechado',
            'SALDO_FECHAMENTO' => $caixaAberto->SALDO_ATUAL,
            'DATA_FECHAMENTO' => Carbon::now(),
        ]);

        return redirect()->route('caixa.index')->with('success', 'Caixa fechado com sucesso.');
    }
}
