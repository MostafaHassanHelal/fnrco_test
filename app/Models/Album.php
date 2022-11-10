<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    
    protected $fillable = [
        'image',
        'model_id',
        'model_type',
    ];
}
