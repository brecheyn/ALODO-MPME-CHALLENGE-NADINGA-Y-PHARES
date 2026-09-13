<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Ordre important : niveaux PUIS dimensions PUIS questions PUIS recommandations
     * (les clés étrangères exigent que les parents existent avant les enfants).
     */
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,        // 1. les 4 niveaux de maturité
            DimensionSeeder::class,    // 2. les 8 dimensions
            QuestionSeeder::class,     // 3. questions + options (dépend de 1 et 2)
            RecommendationSeeder::class, // 4. recommandations (dépend de 1 et 2)
            AdminSeeder::class,          // 5. les admins du back-office
        ]);
    }
}

