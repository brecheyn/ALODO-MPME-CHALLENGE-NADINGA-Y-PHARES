<?php

namespace Database\Seeders;

use App\Models\Dimension;
use Illuminate\Database\Seeder;

class DimensionSeeder extends Seeder
{
    /**
     * Les 8 dimensions évaluées (alignées sur la maquette ALODO MPME).
     */
    public function run(): void
    {
        $dimensions = [
            [
                'code'          => 'tenue_comptes',
                'label'         => 'Tenue des comptes',
                'description'   => 'La qualité et la régularité de votre comptabilité.',
                'display_order' => 1,
            ],
            [
                'code'          => 'gestion_ventes',
                'label'         => 'Gestion des ventes',
                'description'   => 'Le suivi de vos ventes, prix et marges.',
                'display_order' => 2,
            ],
            [
                'code'          => 'tresorerie',
                'label'         => 'Trésorerie',
                'description'   => 'Le suivi de l\'argent disponible et des flux.',
                'display_order' => 3,
            ],
            [
                'code'          => 'budget_previsionnel',
                'label'         => 'Budget prévisionnel',
                'description'   => 'Votre capacité à anticiper dépenses et recettes.',
                'display_order' => 4,
            ],
            [
                'code'          => 'separation_taches',
                'label'         => 'Séparation des tâches',
                'description'   => 'Qui encaisse, qui dépense, qui vérifie.',
                'display_order' => 5,
            ],
            [
                'code'          => 'credit_endettement',
                'label'         => 'Crédit & endettement',
                'description'   => 'La maîtrise de vos emprunts et engagements.',
                'display_order' => 6,
            ],
            [
                'code'          => 'facturation_recouvrement',
                'label'         => 'Facturation & recouvrement',
                'description'   => 'Facturer à temps et être payé à temps.',
                'display_order' => 7,
            ],
            [
                'code'          => 'pilotage_decisions',
                'label'         => 'Pilotage & décisions',
                'description'   => 'L\'usage de vos chiffres pour décider.',
                'display_order' => 8,
            ],
        ];

        foreach ($dimensions as $dimension) {
            Dimension::updateOrCreate(
                ['code' => $dimension['code']],
                $dimension
            );
        }
    }
}
