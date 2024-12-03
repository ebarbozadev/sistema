<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TCG\Voyager\Traits\VoyagerUser;
use TCG\Voyager\Contracts\User as VoyagerUserContract; // Importe a interface
use Laravel\Sanctum\HasApiTokens;

class User extends \TCG\Voyager\Models\User implements VoyagerUserContract // Implemente a interface
{
    use HasApiTokens, HasFactory, Notifiable, VoyagerUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_empresa',
        'name',
        'email',
        'password',
        'teste',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
