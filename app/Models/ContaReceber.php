<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    use HasFactory;

    protected $table = 'tb_contas_receber_tcc'; // Nome da tabela no banco
    protected $fillable = [
        'descricao',
        'valor',
        'status',
        'data_vencimento',
        'data_recebimento',
        'parcela'
    ];
}
