<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EmployeeVacation extends Model
{
    use HasFactory;

    /**
     * Die Attribute, die der Massen-Zuweisung zugeordnet werden können.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'vacation_start_date',
        'vacation_end_date',
        'number_of_vacations',
        'vacation_status',
        'approved_by',
        'apply_date',
        'description',
    ];

    /**
     * Prüfen, ob der Benutzer ein Mitarbeiter ist.
     *
     * @return bool
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function approve()
    {
        return $this->hasOne(User::class,'id', 'approved_by');
    }
}
