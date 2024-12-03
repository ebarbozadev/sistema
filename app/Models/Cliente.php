<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Cliente extends BaseModel
{
    protected $fillable = [
        'nome',
        'email',
        'tipo_pessoa',
        'documento',
        'data_nascimento',
        'telefone_residencial',
        'endereco_residencial',
        'telefone_comercial',
        'endereco_comercial',
        'telefone_outros',
        'endereco_outros',
        'ativo',
        'id_empresa',
        'id_usuario'
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($fornecedor) {
            if (Auth::check()) {
                $fornecedor->id_usuario = Auth::id();
                $fornecedor->id_empresa = Auth::user()->id_empresa;
            }
        });

        static::updating(function ($client) {
            if (Auth::check()) {
                $client->id_usuario = Auth::id();
                $client->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
