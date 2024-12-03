<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
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
            'tipo_pessoa' => 'required|in:F,J',
            'documento' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'ativo' => 'nullable|in:0,1',
            'data_nascimento' => 'nullable|date',
        ]);

        if ($validated) {
            try {
                $user = new User();
                $user->id_empresa = Auth::user()->id_empresa;
                // $user->id_usuario = Auth::user()->id;
                $user->name = $validated['nome'];
                $user->email = $validated['email'];
                $user->password = Hash::make($validated['senha']);
                $user->role_id = 10;
                $user->save();

                $funcionario = new Employee();
                $funcionario->id_usuario = Auth::user()->id;
                $funcionario->id_empresa = Auth::user()->id_empresa;
                $funcionario->nome = $validated['nome'];
                $funcionario->email = $validated['email'];
                $funcionario->senha = Hash::make($validated['senha']);
                $funcionario->tipo_pessoa = $validated['tipo_pessoa'];
                $funcionario->data_nascimento = $validated['data_nascimento'];
                $funcionario->documento = $validated['documento'];
                $funcionario->telefone = $validated['telefone'];
                $funcionario->endereco = $validated['endereco'];
                $funcionario->ativo = $validated['ativo'];

                $funcionario->created_at = now();
                $funcionario->updated_at = now();
                $funcionario->save();

                return redirect()->route('voyager.employees.read')->with('success', 'Funcionário criado com sucesso!');
            } catch (\Exception $e) {
                Log::error('Erro ao criar funcionário: ' . $e->getMessage());

                return redirect('/admin/employees')->with('error', 'Ocorreu um erro ao criar o funcionário. Por favor, tente novamente.');
            }
        }
    }

}
