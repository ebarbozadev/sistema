<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\User_Roller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class EmployeesController extends VoyagerBaseController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'senha' => 'required|string|min:6',
            'tpPessoa' => 'required|in:0,1',
            'documento' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'status' => 'nullable|in:0,1',
            'dtNascimento' => 'nullable|date',
        ]);

        if ($validated) {
            try {
                $user = new User();
                $user->id_empresa = Auth::user()->id_empresa;
                // $user->id_usuario = Auth::user()->id;
                $user->name = $validated['nome'];
                $user->email = $validated['email'];
                $user->password = Hash::make($validated['senha']);
                $user->role_id = 3;
                $user->save();

                $funcionario = new Employee();
                $funcionario->id_usuario = Auth::user()->id;
                $funcionario->id_empresa = Auth::user()->id_empresa;
                $funcionario->nome = $validated['nome'];
                $funcionario->email = $validated['email'];
                $funcionario->tpPessoa = $validated['tpPessoa'];
                $funcionario->dtNascimento = $validated['dtNascimento'];
                $funcionario->documento = $validated['documento'];
                $funcionario->telefone = $validated['telefone'];
                $funcionario->endereco = $validated['endereco'];
                $funcionario->status = $validated['status'];

                $funcionario->created_at = now();
                $funcionario->updated_at = now();
                $funcionario->deleted_at = now();
                $funcionario->save();

                return redirect('/admin/employees')->with('success', 'Funcionário criado com sucesso!');
            } catch (\Exception $e) {
                Log::error('Erro ao criar funcionário: ' . $e->getMessage());

                return redirect('/admin/employees')->with('error', 'Ocorreu um erro ao criar o funcionário. Por favor, tente novamente.');
            }
        }
    }
}
