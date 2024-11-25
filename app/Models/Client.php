<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Client extends BaseModel
{
    protected $fillable = [
        'name',
        'email',
        'tp_people',
        'document',
        'date_of_birth',
        'telephone_res',
        'telephone_res_res',
        'telephone_com',
        'telephone_com_res',
        'telephone_other',
        'telephone_other_res',
        'status',
        'id_empresa',
        'id_usuario'
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($fornecedor) {
            if (Auth::check()) {
                $fornecedor->id_usuario = Auth::id();
                $fornecedor->id_empresa = Auth::user()->id_empresa;
            }
        });

        static::updating(function ($client) {
            if (Auth::check()) {
                $client->id_usuario = Auth::id();
                $client->id_empresa = Auth::user()->id_empresa;
            }
        });
    }
}
