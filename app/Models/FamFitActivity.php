<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamFitActivity extends Model
{
    protected $table = 'calendars';

    protected $fillable = [
        'user_id',
        'title',
        'activity_type',
        'activity_date',
        'start_time',
        'reminder_minutes_before',
        'end_time',
        'duration_minutes',
        'notes',
        'status',
        'points',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'duration_minutes' => 'integer',
        'reminder_minutes_before' => 'integer',
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
