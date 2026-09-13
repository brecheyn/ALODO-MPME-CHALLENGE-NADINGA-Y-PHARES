<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Recommendation;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    /**
     * Une recommandation actionnable par dimension × niveau (textes courts,
     * alignés sur la maquette : "une force, une priorité, une action concrète").
     */
    public function run(): void
    {
        $data = [
            'tenue_comptes' => [
                1 => ['Ouvrir un cahier de comptes', 'Notez chaque jour vos recettes et dépenses dans un cahier dédié.'],
                2 => ['Tenir un registre hebdomadaire', 'Réservez 15 minutes chaque semaine pour mettre à jour vos comptes.'],
                3 => ['Passer à un suivi mensuel', 'Éditez un récapitulatif mensuel de vos recettes et dépenses.'],
                4 => ['Consolider vos comptes', 'Vérifiez régulièrement la cohérence entre vos registres et vos relevés.'],
            ],
            'gestion_ventes' => [
                1 => ['Compter vos ventes', 'Notez chaque vente du jour : produit, quantité, montant.'],
                2 => ['Suivre vos marges', 'Calculez la marge de vos 3 produits les plus vendus ce mois-ci.'],
                3 => ['Analyser par produit', 'Identifiez vos produits les plus rentables pour orienter vos achats.'],
                4 => ['Optimiser vos prix', 'Comparez vos marges avec le marché pour ajuster vos tarifs.'],
            ],
            'tresorerie' => [
                1 => ['Connaître votre caisse', 'Comptez l\'argent disponible chaque matin avant d\'ouvrir.'],
                2 => ['Suivre une trésorerie hebdomadaire', 'Établissez chaque semaine le total encaissé et dépensé.'],
                3 => ['Prévoir vos soldes à 30 jours', 'Anticipez votre argent disponible pour les 30 prochains jours.'],
                4 => ['Sécuriser une réserve', 'Constituez une réserve couvrant un mois de charges fixes.'],
            ],
            'budget_previsionnel' => [
                1 => ['Lister vos échéances', 'Notez toutes vos grosses dépenses prévisibles des 3 prochains mois.'],
                2 => ['Planifier vos dépenses', 'Répartissez vos grosses charges sur les mois à venir.'],
                3 => ['Établir un budget mensuel', 'Fixez un plafond de dépenses par catégorie et suivez-le.'],
                4 => ['Projeter sur 12 mois', 'Construisez un budget annuel pour préparer vos investissements.'],
            ],
            'separation_taches' => [
                1 => ['Séparer caisse et registre', 'Gardez l\'argent et le registre à des moments différents de la journée.'],
                2 => ['Faire vérifier par un tiers', 'Faites contrôler vos comptes une fois par mois par une autre personne.'],
                3 => ['Répartir les rôles', 'Confiez l\'encaissement et la vérification à deux personnes distinctes.'],
                4 => ['Formaliser les contrôles', 'Documentez vos procédures de contrôle interne par écrit.'],
            ],
            'credit_endettement' => [
                1 => ['Lister toutes vos dettes', 'Notez chaque dette : à qui, combien, quand payer.'],
                2 => ['Calendrier de remboursement', 'Planifiez chaque échéance avec les montants exacts.'],
                3 => ['Suivre votre capacité d\'emprunt', 'Calculez le poids de vos échéances dans vos revenus mensuels.'],
                4 => ['Négocier vos financements', 'Comparez plusieurs offres avant tout nouvel emprunt.'],
            ],
            'facturation_recouvrement' => [
                1 => ['Fixer un délai de paiement', 'Annoncez systématiquement un délai clair à chaque client.'],
                2 => ['Relancer vos clients', 'Programmez une relance 7 jours après chaque paiement attendu.'],
                3 => ['Suivre vos créances', 'Tenez un tableau des factures en attente avec leurs échéances.'],
                4 => ['Sécuriser vos encaissements', 'Demandez des acomptes pour les commandes importantes.'],
            ],
            'pilotage_decisions' => [
                1 => ['Relire vos chiffres chaque semaine', 'Passez 10 minutes chaque semaine à relire votre cahier de comptes.'],
                2 => ['Comparer les mois', 'Comparez ce mois au mois précédent : ventes, dépenses, résultat.'],
                3 => ['Décider sur la base des chiffres', 'Avant chaque grosse décision, vérifiez vos 3 indicateurs clés.'],
                4 => ['Instaurer un point de gestion mensuel', 'Bloquez une heure par mois pour piloter avec vos chiffres.'],
            ],
        ];

        foreach ($data as $code => $levels) {
            $dimension = Dimension::where('code', $code)->firstOrFail();

            foreach ($levels as $levelNumber => $texts) {
                Recommendation::updateOrCreate(
                    [
                        'dimension_id' => $dimension->id,
                        'level_id'     => $levelNumber,
                    ],
                    [
                        'title'       => $texts[0],
                        'action_text' => $texts[1],
                    ]
                );
            }
        }
    }
}
