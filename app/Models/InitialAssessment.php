<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialAssessment extends Model
{
    use HasFactory;

    // 1. Le decimos a Laravel qué columnas puede guardar
    protected $fillable = [
        'user_id',
        'questionnaire_version',
        'main_goal',
        'experience_level',
        'sessions_per_week',
        'session_minutes',
        'preferred_environments',
        'equipment',
        'sleep_habits',
        'social_interests',
        'answers',
        'is_current',
        'completed_at',
    ];

    // 2. Le decimos que los campos JSON se manejen como arrays en PHP
    protected $casts = [
        'equipment' => 'array',
        'sleep_habits' => 'array',
        'social_interests' => 'array',
        'answers' => 'array',
        'is_current' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // 3. Relación inversa: Un test pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}