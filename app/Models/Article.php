<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * Die Felder, die massenweise zuweisbar sind.
     */
    protected $fillable = [
        'article_number',  
        'description',
    ];
}
