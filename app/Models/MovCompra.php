<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovCompra extends Model
{
    use SoftDeletes;

    protected $table = 'mov_compras';

    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'id_fornecedor',
        'data_compra',
        'vl_total',
        'vl_desconto',
        'vl_desconto_pr',
        'vl_liquido',
        'status',
    ];

    protected $dates = ['data_compra', 'created_at', 'updated_at', 'deleted_at'];

    public function itens()
    {
        return $this->hasMany(MovCompraIten::class, 'id_mov_compra', 'id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedore::class, 'id_fornecedor', 'id');
    }
}