<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovVenda extends Model
{
    protected $table = 'mov_vendas'; // Certifique-se de que este é o nome correto da tabela

    // Se a chave primária não for 'id', especifique-a
    protected $primaryKey = 'ID'; // Substitua por 'ID' ou o nome correto da chave primária

    public $incrementing = true; // Confirma que a chave primária é auto-incremento

    protected $keyType = 'int'; // Especifica o tipo da chave primária

    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'id_cliente',
        'DATA_VENDA',
        'VL_TOTAL',
        'VL_DESCONTO',
        'VL_LIQUIDO',
        'STATUS',
    ];


    public $timestamps = false; // Se a tabela não possui os campos created_at e updated_at

}
