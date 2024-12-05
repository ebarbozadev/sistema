<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CaixaController extends Controller
{
    public function index()
    {
        // Buscar todos os caixas, possivelmente paginados
        $caixas = Caixa::orderBy('id', 'desc')->paginate(10); // Ajuste conforme necessário

        // Obter o caixa aberto
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        // Obter caixas fechados
        $caixasFechados = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Fechado')
            ->orderBy('data_fechamento', 'desc')
            ->get();

        return view('caixa.index', compact('caixas', 'caixaAberto', 'caixasFechados'));
    }

    /**
     * Exibe o formulário para abrir um novo caixa.
     */
    public function mostrarFormularioAbertura()
    {
        // Verificar se já existe um caixa aberto para a empresa
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if ($caixaAberto) {
            return redirect()->route('caixa.index')->with('error', 'Já existe um caixa aberto. Feche-o antes de abrir um novo.');
        }

        return view('caixa.abrir');
    }

    /**
     * Processa a abertura de um novo caixa.
     */
    public function abrirCaixa(Request $request)
    {
        // Verificar se já existe um caixa aberto para a empresa
        $caixaAberto = Caixa::where('id_empresa', Auth::user()->id_empresa)
            ->where('status', 'Aberto')
            ->first();

        if ($caixaAberto) {
            return redirect()->route('caixa.index')->with('error', 'Já existe um caixa aberto. Feche-o antes de abrir um novo.');
        }

        // Validação dos dados de abertura do caixa
        $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Criação do caixa
            $caixa = Caixa::create([
                'id_empresa' => Auth::user()->id_empresa,
                'id_usuario' => Auth::id(),
                'saldo_inicial' => $request->input('saldo_inicial'),
                'saldo_atual' => $request->input('saldo_inicial'), // Inicializa saldo_atual com saldo_inicial
                'status' => 'Aberto',
                'data_abertura' => now(), // Define a data de abertura
            ]);

            DB::commit();

            Log::info("Caixa ID {$caixa->id} aberto com sucesso.", [
                'saldo_inicial' => $caixa->saldo_inicial,
                'saldo_atual' => $caixa->saldo_atual,
                'data_abertura' => $caixa->data_abertura,
            ]);

            return redirect()->route('caixa.detalhes', $caixa->id)->with('success', 'Caixa aberto com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao abrir caixa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao abrir caixa: ' . $e->getMessage());
        }
    }

    public function mostrarFechamento($id)
    {
        $caixa = Caixa::findOrFail($id);

        if ($caixa->status !== 'Aberto') {
            return redirect()->back()->with('error', 'O caixa já está fechado.');
        }

        return view('caixa.fechar', compact('caixa'));
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
            return redirect()->back()->with('error', 'O caixa já está fechado.');
        }

        // Atualiza o caixa com os dados de fechamento
        $caixa->update([
            'saldo_fechamento' => $caixa->saldo_atual, // Define saldo_fechamento como saldo_atual
            'data_fechamento' => now(),
            'status' => 'Fechado',
        ]);

        Log::info("Caixa ID {$caixa->id} fechado com sucesso.", [
            'saldo_fechamento' => $caixa->saldo_fechamento,
            'data_fechamento' => $caixa->data_fechamento,
            'status' => $caixa->status,
        ]);

        return redirect()->route('caixa.index')->with('success', 'Caixa fechado com sucesso!');
    }

    public function detalhesCaixa($id)
    {
        $caixa = Caixa::with(['movimentacoes', 'vendas.itens.produto'])->findOrFail($id);

        // Obter todas as vendas associadas a esse caixa com itens e produtos
        $vendas = $caixa->vendas()->with('itens.produto')->get();

        return view('caixa.detalhes', compact('caixa', 'vendas'));
    }
}
