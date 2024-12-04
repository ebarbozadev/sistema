<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaPagar extends Model
{
    use HasFactory;

    protected $table = 'pagar_contas';

    protected $fillable = [
        'id_empresa',
        'id_usuario',
        'id_fornecedor',
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'parcela',
        'status',
        'data_criacao',
        'data_atualizacao',
    ];

    const UPDATED_AT = 'data_atualizacao';
    const CREATED_AT = 'data_criacao';

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedore::class, 'id_fornecedor');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
