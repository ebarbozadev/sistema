<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovCompraIten extends Model
{
    use SoftDeletes;

    protected $table = 'mov_compra_itens';

    protected $fillable = [
        'id_usuario',
        'id_fornecedor',
        'id_empresa',
        'id_mov_compra',
        'sequencia',
        'quantidade',
        'vl_unitario',
        'vl_total',
        'vl_desconto',
        'vl_desconto_pr',
        'vl_liquido',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function movCompra()
    {
        return $this->belongsTo(MovCompra::class, 'id_mov_compra', 'id');
    }
}
