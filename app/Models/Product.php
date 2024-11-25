<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Nome da tabela (se não seguir o padrão "products")
    protected $table = 'produtos';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'id_empresa',
        'id_categoria',
        'id_marca',
        'id_linha',
        'nome',
        'sexo',
        'estoque',
        'estoqueMinimo',
        'precoCusto',
        'precoVenda',
        'status',
        'descricao',
        'images',
        'slug',
        'id_usuario',
    ];

    // Campos que serão tratados como datas
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relacionamento com a empresa.
     * Exemplo: Um produto pertence a uma empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    /**
     * Relacionamento com o usuário.
     * Exemplo: Um produto pertence a um usuário.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Relacionamento com a categoria.
     * Exemplo: Um produto pertence a uma categoria.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
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
        return $query->where('status', 1);
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
        return $this->status ? 'Ativo' : 'Inativo';
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
}
