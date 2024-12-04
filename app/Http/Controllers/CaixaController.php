<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaixaController extends Controller
{
    public function index()
    {
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        return view('caixa.index', compact('caixaAberto'));
    }

    public function abrirCaixa(Request $request)
    {
        $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
        ]);

        // Verifica se já existe um caixa aberto
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if ($caixaAberto) {
            return redirect()->back()->with('error', 'Já existe um caixa aberto.');
        }

        // Cria um novo caixa
        $caixa = Caixa::create([
            'id_empresa' => Auth::user()->id_empresa,
            'id_usuario' => Auth::id(),
            'saldo_inicial' => $request->input('saldo_inicial'),
            'saldo_atual' => $request->input('saldo_inicial'),
            'status' => 'Aberto',
            'data_abertura' => now(),
        ]);

        return redirect()->route('caixa.index')->with('success', 'Caixa aberto com sucesso!');
    }

    public function verificarCaixa()
    {
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if ($caixaAberto) {
            $dataAbertura = $caixaAberto->data_abertura->format('Y-m-d');
            $hoje = now()->format('Y-m-d');

            if ($dataAbertura != $hoje) {
                // Caixa aberto não é de hoje, precisa fechar
                return view('caixa.fechar', compact('caixaAberto'));
            }
        } else {
            // Não há caixa aberto, redireciona para abrir um novo caixa
            return redirect()->route('caixa.index')->with('error', 'Não há caixa aberto. Abra um caixa para continuar.');
        }

        // Caixa está ok
        return redirect()->route('sales.sales'); // Ajuste para a rota correta
    }

    // Método para fechar o caixa anterior
    public function fecharCaixaAnterior()
    {
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if (!$caixaAberto) {
            // Não há caixa aberto, redireciona para abrir um novo caixa
            return redirect()->route('caixa.index')->with('error', 'Não há caixa aberto para fechar.');
        }

        return view('caixa.fechar_anterior', compact('caixaAberto'));
    }

    // Método para processar o fechamento do caixa anterior
    public function processarFechamentoAnterior(Request $request)
    {
        $request->validate([
            'saldo_fechamento' => 'required|numeric',
        ]);

        $caixa = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if (!$caixa) {
            return redirect()->route('caixa.index')->with('error', 'Não há caixa aberto para fechar.');
        }

        // Atualiza o caixa com os dados de fechamento
        $caixa->update([
            'saldo_fechamento' => $request->input('saldo_fechamento'),
            'data_fechamento' => now(),
            'status' => 'Fechado',
        ]);

        return redirect()->route('sales.sales')->with('success', 'Caixa fechado com sucesso!');
    }


    public function fecharCaixa(Request $request, $id)
    {
        $caixa = Caixa::findOrFail($id);

        if ($caixa->status !== 'Aberto') {
            return redirect()->back()->with('error', 'O caixa já está fechado ou não pode ser fechado.');
        }

        // Atualiza o caixa com os dados de fechamento
        $caixa->update([
            'saldo_fechamento' => $caixa->saldo_atual,
            'status' => 'Fechado',
            'data_fechamento' => now(),
        ]);

        return redirect()->route('caixa.index')->with('success', 'Caixa fechado com sucesso!');
    }

    public function detalhesCaixa($id)
    {
        $caixa = Caixa::with('movimentacoes')->findOrFail($id);

        return view('caixa.detalhes', compact('caixa'));
    }
}
