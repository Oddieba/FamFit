<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'age',
        'weight',
        'height',
        'gender',
        'goals',
        'experience',
        'availability',
        'has_injury',
        'injury_description',
    ];

    protected function casts(): array
    {
        return [
            'availability' => 'array',
            'has_injury' => 'boolean',
        ];
    }
}