<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Linha extends Model
{
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
