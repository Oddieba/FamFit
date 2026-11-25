<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    // Permitimos la asignación masiva de estos campos
    protected $fillable = [
        'slug', 
        'name', 
        'environments', 
        'objective', 
        'muscle_group', 
        'target_zone', 
        'equipment', 
        'difficulty', 
        'intensity', 
        'instructions', 
        'common_mistakes', 
        'responsible_use_note', 
        'is_active', 
        'created_by'
    ];

    // Relación inversa: Un ejercicio puede estar en muchas rutinas
    public function routines()
    {
        return $this->belongsToMany(Routine::class, 'routine_exercises')
                    ->withPivot('position', 'sets', 'reps', 'duration_seconds', 'rest_seconds')
                    ->withTimestamps();
    }
}