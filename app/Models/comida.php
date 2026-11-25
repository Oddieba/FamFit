<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comida extends Model
{
    protected $table = 'comidas';

    protected $fillable = [
        'user_id', 'fecha', 'tipo', 'hora', 'descripcion', 'notas', 'origen',
    ];

    public function alimentos()
    {
        return $this->hasMany(ComidaAlimento::class, 'comida_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoComida::class, 'comida_id');
    }
}