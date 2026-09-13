<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dimension extends Model
{
    protected $table = 'dimensions';
    protected $fillable = [
        'code',          // identifiant technique : "liquidity", "governance"...
        'label',         // nom affiché : "Liquidité", "Tenue des comptes"...
        'description',   // texte explicatif optionnel
        'display_order', // position d'affichage (1 à 8)
        'weight',        // pondération dans le score global
    ];

    // Une dimension possède PLUSIEURS questions (relation 1-N), triées par ordre d'affichage
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('display_order');
    }
}
