<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovVenda extends Model
{
    protected $table = 'mov_vendas';
    protected $primaryKey = 'id'; // Certifique-se de que a chave primária é 'id'
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'id_cliente',
        'data_venda',
        'id_caixa', // Adicionado para mass assignment
        'vl_total',
        'vl_desconto',
        'vl_liquido',
        'status',
    ];

    // Relacionamento com os itens da venda
    public function itens()
    {
        return $this->hasMany(MovVendaIten::class, 'id_mov_venda', 'id');
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class, 'id_caixa', 'id');
    }

    public function movimentacoesCaixa()
    {
        return $this->hasMany(MovCaixa::class, 'id_movimento', 'id');
    }
}
