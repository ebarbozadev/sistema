<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ScopeModel implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            // Se o usuário for admin, não aplica o filtro
            if (Auth::user()->role->name == 'admin') {
                return;
            }

            // Filtra pelo id_empresa do usuário logado
            $builder->where('id_empresa', Auth::user()->id_empresa);
        }
    }
}
