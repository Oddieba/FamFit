<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComidaAlimento extends Model
{
    protected $table = 'comida_alimentos';

    protected $fillable = ['comida_id', 'nombre', 'porcion', 'confianza'];
}