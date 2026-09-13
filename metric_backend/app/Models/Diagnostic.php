<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Diagnostic extends Model
{
    protected $fillable = [
        'token',   // identifiant de session anonyme
        'status',  // "in_progress" | "completed"
        'email',   // email facultatif
        'level_id', // niveau attribué après calcul du résultat
    ];

    // Un diagnostic possède PLUSIEURS réponses
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    // Un diagnostic a UN seul résultat (calculé à la fin)
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    // Le niveau attribué au diagnostic (nullable jusqu'à complétion)
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
