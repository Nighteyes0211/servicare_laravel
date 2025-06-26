<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Name der Aufgabe
        'icon', // Feld für das Icon
        'color', // Farbe

    ];


    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'category_task');
    }
}
