<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Profile;
use App\Models\InitialAssessment;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role_id',
        'username',
        'email',
        'password',
        'terms_accepted_at',
        'privacy_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_visible' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'privacy_accepted_at' => 'datetime',
            'avatar_settings' => 'array',
            'preferences' => 'array',
            'xp' => 'integer',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function questRoutine(): HasOne
    {
        return $this->hasOne(QuestRoutine::class);
    }

    public function questCompletions(): HasMany
    {
        return $this->hasMany(QuestExerciseCompletion::class);
    }

    // Relación con los Tests Iniciales (1 a muchos)
    public function initialAssessments()
    {
        return $this->hasMany(InitialAssessment::class);
    }

    // Obtener el test actual (el que está activo)
    public function currentAssessment()
    {
        return $this->hasOne(InitialAssessment::class)->where('is_current', true);
    }

}

