<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    // Permitimos que Laravel guarde datos en estas columnas
    protected $fillable = [
        'owner_user_id',
        'name',
        'description',
        'environment',
        'objective',
        'difficulty',
        'estimated_minutes',
        'source',
        'visibility',
        'publication_status',
        'published_at',
        'is_active',
        'created_by'
    ];

    // Relación con los ejercicios (opcional por ahora, pero útil para después)
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'routine_exercises')
                    ->withPivot('position', 'sets', 'reps', 'duration_seconds', 'rest_seconds')
                    ->withTimestamps()
                    ->orderBy('position');
    }
}