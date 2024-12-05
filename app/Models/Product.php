<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends BaseModel
{
    use HasFactory, SoftDeletes;

    // Nome da tabela (se não seguir o padrão "products")
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'id_empresa',
        'id_fornecedor',
        'id_usuario',
        'nome',
        'descricao',
        'imagens',
        'sexo',
        'estoque',
        'estoque_minimo',
        'preco_custo',
        'preco_venda',
        'ativo',
        'id_categoria',
        'id_marca',
        'id_linha',
    ];

    public $timestamps = true;


    // Campos que serão tratados como datas
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    /**
     * Relacionamento com o usuário.
     * Exemplo: Um produto pertence a um usuário.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Relacionamento com o usuário.
     * Exemplo: Um produto pertence a um usuário.
     */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedore::class, 'id_fornecedor');
    }

    /**
     * Relacionamento com a categoria.
     * Exemplo: Um produto pertence a uma categoria.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function itensVenda()
    {
        return $this->hasMany(MovVendaIten::class, 'product_id', 'id');
    }

    public function movCompraItens()
    {
        return $this->hasMany(MovCompraIten::class, 'id_produto', 'id');
    }

    /**
     * Relacionamento com a marca.
     * Exemplo: Um produto pertence a uma marca.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    /**
     * Relacionamento com a linha.
     * Exemplo: Um produto pertence a uma linha.
     */
    public function linha()
    {
        return $this->belongsTo(Linha::class, 'id_linha');
    }

    /**
     * Escopo para buscar produtos ativos.
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', '1');
    }

    /**
     * Escopo para buscar produtos de uma empresa específica.
     */
    public function scopeByEmpresa($query, $empresaId)
    {
        return $query->where('id_empresa', $empresaId);
    }

    /**
     * Acessor para retornar o status como texto.
     */
    public function getStatusTextAttribute()
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    /**
     * Acessor para retornar o sexo como texto.
     */
    public function getSexoTextAttribute()
    {
        return match ($this->sexo) {
            'M' => 'Masculino',
            'F' => 'Feminino',
            'U' => 'Unissex',
            default => 'Não especificado',
        };
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
