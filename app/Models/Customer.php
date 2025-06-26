<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use App\Models\Contact;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'phone',
        'email',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
