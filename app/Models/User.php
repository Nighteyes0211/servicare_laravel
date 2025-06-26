<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Task;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Die Attribute, die der Massen-Zuweisung zugeordnet werden können.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'vacation_days',
        'vacation_days_left',
        'four_day_week'
    ];


    /**
     * Die Attribute, die bei Serialisierung versteckt werden sollen.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Die Typen der Attribute, die gecastet werden sollen.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Prüfen, ob der Benutzer ein Admin ist.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Prüfen, ob der Benutzer ein Mitarbeiter ist.
     *
     * @return bool
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    // In the User model (App\Models\User.php)
    public function tasks()
    {
        return $this->hasMany(Task::class); // Assuming a User can have many Tasks
    }

}
