<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestExerciseCompletion extends Model
{
    protected $fillable = ['user_id', 'quest_exercise_id', 'completed_on'];

    protected function casts(): array
    {
        return ['completed_on' => 'date'];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(QuestExercise::class, 'quest_exercise_id');
    }
}
