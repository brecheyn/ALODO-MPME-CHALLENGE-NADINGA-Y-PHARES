<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'dimension_id',  // la dimension de rattachement
        'text',          // l'énoncé de la question
        'display_order', // ordre dans la dimension
    ];

    // La question appartient à UNE dimension (relation N-1)
    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class);
    }

    // Une question possède PLUSIEURS options de réponse (relation 1-N)
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }
}
