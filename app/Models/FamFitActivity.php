<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamFitActivity extends Model
{
    protected $table = 'calendars';

    protected $fillable = [
        'title',
        'activity_type',
        'activity_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'notes',
        'status',
        'points',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'duration_minutes' => 'integer',
        'points' => 'integer',
    ];
}
