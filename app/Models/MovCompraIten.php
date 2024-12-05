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

    /**
     * Relacionamento com a compra.
     */
    public function movCompra()
    {
        return $this->belongsTo(MovCompra::class, 'id_mov_compra', 'id');
    }

    /**
     * Relacionamento com o fornecedor.
     */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedore::class, 'id_fornecedor', 'id');
    }

    /**
     * Relacionamento com o usuário que adicionou o item.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    /**
     * Relacionamento com a empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id');
    }

    /**
     * Relacionamento com o produto.
     * Assumindo que há um relacionamento com Produto.
     */
    public function produto()
    {
        return $this->belongsTo(Product::class, 'id_produto', 'id'); // Certifique-se de ter a coluna 'id_produto'
    }
}
