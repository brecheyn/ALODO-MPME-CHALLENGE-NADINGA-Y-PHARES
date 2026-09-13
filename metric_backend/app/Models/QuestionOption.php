<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Convention : nom de classe en StudlyCase -> "QuestionOption"
// => table "question_options" déduite automatiquement (pluriel snake_case)
class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',   // la question à laquelle appartient l'option
        'text',          // le libellé affiché à l'utilisateur
        'score',         // points rapportés si choisie (0 à 3) - moteur de scoring
        'display_order', // ordre d'affichage
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // Une option appartient à UNE question (relation N-1)
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
