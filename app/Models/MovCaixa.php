<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovCaixa extends Model
{
    protected $table = 'mov_caixas';
    protected $fillable = ['ID_CAIXA', 'ID_EMPRESA', 'ID_USUARIO', 'TIPO_MOVIMENTACAO', 'DESCRICAO', 'VALOR', 'DATA_MOVIMENTACAO'];
}
