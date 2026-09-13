<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'diagnostic_id', // le diagnostic terminé
        'global_score',  // score global 0-100
        'level_id',      // niveau de maturité atteint
        'strength_text', // point fort ("À préserver")
        'priority_text', // priorité ("À structurer")
        'details',       // scores par dimension en JSON : {"tenue_comptes": 86, ...}
    ];

    protected $casts = [
        'global_score' => 'integer',
        'details'      => 'array', // Laravel convertit automatiquement le JSON en tableau PHP
    ];

    public function diagnostic(): BelongsTo
    {
        return $this->belongsTo(Diagnostic::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
