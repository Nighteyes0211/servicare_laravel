<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

         protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'due_date' => 'datetime',
    ];
  
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
{
    return $this->belongsToMany(TaskCategory::class, 'category_task');
}



public function contact()
{
    return $this->belongsTo(Contact::class, 'contact_id');
}






public function getStatusDeAttribute()
{
    return match ($this->status) {
        'open' => 'Offen',
        'done' => 'Erledigt',
        'not_done' => 'Nicht erledigt',
        'billed' => 'Abgerechnet',
        default => ucfirst($this->status),
    };
}

}


