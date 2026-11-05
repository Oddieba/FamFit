<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestExercise extends Model
{
    protected $fillable = [
        'name',
        'day_of_week',
        'sets',
        'reps',
        'rest_seconds',
        'position',
    ];

    public function routine(): BelongsTo
    {
        return $this->belongsTo(QuestRoutine::class, 'quest_routine_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(QuestExerciseCompletion::class);
    }
}
