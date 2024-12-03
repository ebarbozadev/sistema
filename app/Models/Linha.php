<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Linha extends BaseModel
{
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($linha) {
            if (Auth::check()) {
                $linha->id_usuario = Auth::id();
                $linha->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
