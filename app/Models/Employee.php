<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Employee extends BaseModel
{
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function ($funcionario) {
            if (Auth::check()) {
                $funcionario->id_usuario = Auth::id();
                $funcionario->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
