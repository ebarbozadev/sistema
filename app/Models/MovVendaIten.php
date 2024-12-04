<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovVendaIten extends Model
{
    protected $table = 'mov_venda_itens'; // Certifique-se de que este é o nome correto da tabela

    protected $fillable = [
        'ID_MOV_VENDA',
        'SEQUENCIA',
        'QUANTIDADE',
        'VL_UNITARIO',
        'VL_TOTAL',
        'VL_LIQUIDO',
        'id_usuario',
        'id_empresa',
        'id_cliente', // Adicione este campo
    ];

    public $timestamps = false;

    // Se a chave primária não for 'id', especifique-a
    protected $primaryKey = 'ID'; // Substitua por 'ID' ou o nome correto da chave primária

    public $incrementing = true;

    protected $keyType = 'int';
}
