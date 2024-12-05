<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovCaixa extends Model
{
    use HasFactory;

    protected $table = 'mov_caixas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Defina os campos que podem ser preenchidos via mass assignment
    protected $fillable = [
        'id_caixa',
        'id_empresa',
        'id_usuario',
        'id_movimento',
        'tipo_movimentacao',
        'descricao',
        'valor',
        'data_movimentacao',
    ];

    // Relacionamento com Caixa
    public function caixa()
    {
        return $this->belongsTo(Caixa::class, 'id_caixa', 'id');
    }

    // Relacionamento com MovVenda (ou outro modelo relacionado a 'id_movimento')
    public function movimento()
    {
        return $this->belongsTo(MovVenda::class, 'id_movimento', 'id');
    }
}
