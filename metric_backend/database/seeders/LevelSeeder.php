<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Les 4 niveaux de maturité (issus de la maquette : "Niveau 3 · En structuration").
     * Fourchettes de score global sur 100.
     */
    public function run(): void
    {
        $levels = [
            [
                'level_number'  => 1,
                'level_name'    => 'Niveau 1',
                'label'         => 'En démarrage',
                'min_score'     => 0,
                'max_score'     => 25,
                'encouragement' => 'Chaque grande entreprise a commencé ici. Vos premières bases vont vite porter leurs fruits.',
            ],
            [
                'level_number'  => 2,
                'level_name'    => 'Niveau 2',
                'label'         => 'En construction',
                'min_score'     => 26,
                'max_score'     => 50,
                'encouragement' => 'Vos bases sont en place. Avec un peu de méthode, votre gestion devient un vrai atout.',
            ],
            [
                'level_number'  => 3,
                'level_name'    => 'Niveau 3',
                'label'         => 'En structuration',
                'min_score'     => 51,
                'max_score'     => 75,
                'encouragement' => 'Votre prochain progrès est à portée de main. Quelques habitudes suffisent pour décider sereinement.',
            ],
            [
                'level_number'  => 4,
                'level_name'    => 'Niveau 4',
                'label'         => 'Structuré',
                'min_score'     => 76,
                'max_score'     => 100,
                'encouragement' => 'Votre gestion est solide. Vous êtes prêt à chercher un financement en confiance.',
            ],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(
                ['level_number' => $level['level_number']],
                $level
            );
        }
    }
}
