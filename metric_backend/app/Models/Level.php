<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';
    protected $fillable = [
        'level_number',
        'level_name',
        'label',
        'max_score',
        'min_score',
        'encouragement',
    ];
}
