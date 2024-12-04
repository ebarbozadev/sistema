<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caixa extends Model
{
    use SoftDeletes;

    protected $table = 'caixas';

    protected $primaryKey = 'id'; // Verifique se está 'id' ou 'ID'

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'id_empresa',
        'id_usuario',
        'saldo_inicial',
        'saldo_atual',
        'saldo_fechamento',
        'data_abertura',
        'data_fechamento',
        'status',
    ];

    protected $dates = [
        'data_abertura',
        'data_fechamento',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovCaixa::class, 'id_caixa');
    }
}
