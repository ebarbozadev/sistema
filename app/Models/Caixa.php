<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    protected $table = 'caixas';
    protected $fillable = ['ID_EMPRESA', 'SALDO_INICIAL', 'SALDO_ATUAL', 'SALDO_FECHAMENTO', 'DATA_ABERTURA', 'DATA_FECHAMENTO', 'STATUS'];
}
