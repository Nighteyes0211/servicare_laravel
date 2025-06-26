<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;

    //     protected $fillable = ['customer_id', 'diagnosis_details', 'diagnosis_date',    'customer_id',
    //     'diagnosis_details',
    //     'diagnosis_date',
    //     'repair',
    //     'complaint',
    //     'confirmation',
    //     'forwarded_to',
    //     'notes',
    // ];

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
