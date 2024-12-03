<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Marca extends BaseModel
{
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($marca) {
            if (Auth::check()) {
                $marca->id_usuario = Auth::id();
                $marca->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
