<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Categoria extends BaseModel
{
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($categoria) {
            if (Auth::check()) {
                $categoria->id_usuario = Auth::id();
                $categoria->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
