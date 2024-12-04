<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovCaixa extends Model
{
    protected $table = 'mov_caixas';

    protected $primaryKey = 'ID';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ID_CAIXA',
        'ID_EMPRESA',
        'ID_USUARIO',
        'ID_MOVIMENTO', // Certifique-se de que este campo está incluído
        'TIPO_MOVIMENTACAO',
        'DESCRICAO',
        'VALOR',
        'DATA_MOVIMENTACAO',
    ];

    protected $dates = [
        'DATA_MOVIMENTACAO',
    ];
}
