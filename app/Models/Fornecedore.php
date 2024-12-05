<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Fornecedore extends Model
{
    use SoftDeletes;

    protected $table = 'fornecedores';

    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'fantasia',
        'razaosocial',
        'email',
        'tipo_pessoa',
        'documento',
        'endereco',
        'telefone',
        'ativo'
        // Outros campos relevantes
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relacionamento com a Empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id');
    }

    /**
     * Relacionamento com as compras.
     */
    public function movCompras()
    {
        return $this->hasMany(MovCompra::class, 'id_fornecedor', 'id');
    }

    /**
     * Relacionamento com os itens de compra.
     */
    public function movCompraItens()
    {
        return $this->hasMany(MovCompraIten::class, 'id_fornecedor', 'id');
    }
    
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($fornecedor) {
            if (Auth::check()) {
                $fornecedor->id_usuario = Auth::id();
                $fornecedor->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
