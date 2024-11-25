<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ScopeModel;

class BaseModel extends Model
{
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope(new ScopeModel);
    }
}
