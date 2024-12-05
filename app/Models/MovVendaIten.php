<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovVendaIten extends Model
{
    protected $table = 'mov_venda_itens';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_mov_venda',
        'sequencia',
        'quantidade',
        'vl_unitario',
        'vl_total',
        'vl_liquido',
        'product_id',
        'id_usuario',
        'id_empresa',
        'id_cliente',
    ];

    public function venda()
    {
        return $this->belongsTo(MovVenda::class, 'id_mov_venda', 'id');
    }

    public function produto()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
