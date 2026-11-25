<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestriccionUsuario extends Model
{
    protected $table = 'restricciones_usuario';

    protected $fillable = ['user_id', 'texto', 'tipo'];
}
