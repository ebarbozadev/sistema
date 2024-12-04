<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    use HasFactory;

    protected $table = 'receber_contas';

    protected $fillable = [
        'id_empresa',
        'id_usuario',
        'id_cliente',
        'id_fornecedor',
        'valor',
        'data_vencimento',
        'data_pagar',
        'parcela',
        'status',
        'descricao',
    ];

    // Se você tiver colunas de timestamp personalizadas
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Se estiver usando soft deletes
    protected $dates = ['deleted_at'];
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
