<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoComida extends Model
{
    protected $table = 'fotos_comida';

    protected $fillable = ['comida_id', 'ruta'];
}