<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaPagar extends Model
{
    use HasFactory;

    protected $table = 'pagar_contas';

    protected $fillable = [
        'ID_EMPRESA',
        'ID_USUARIO',
        'ID_FORNECEDOR',
        'DESCRICAO',
        'VALOR',
        'DATA_VENCIMENTO',
        'DATA_PAGAMENTO',
        'PARCELA',
        'STATUS',
        'DATA_CRIACAO',
        'DATA_ATUALIZACAO',
    ];

    const UPDATED_AT = 'DATA_ATUALIZACAO';
    const CREATED_AT = 'DATA_CRIACAO';

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedore::class, 'ID_FORNECEDOR');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'ID_EMPRESA');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ID_USUARIO');
    }
}
