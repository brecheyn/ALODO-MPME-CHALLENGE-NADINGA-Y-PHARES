<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * 1 question par dimension ("8 questions simples" - maquette ALODO),
     * 4 options de réponse score de 0 (jamais) à 3 (systématiquement).
     */
    public function run(): void
    {
        $questions = [
            'tenue_comptes' => [
                'text'    => 'Tenez-vous régulièrement un registre ou un cahier de comptes pour votre activité ?',
                'options' => [
                    ['text' => 'Non, jamais', 'score' => 0],
                    ['text' => 'Rarement, quand j\'y pense', 'score' => 1],
                    ['text' => 'Oui, parfois mais irrégulièrement', 'score' => 2],
                    ['text' => 'Oui, chaque jour ou chaque semaine', 'score' => 3],
                ],
            ],
            'gestion_ventes' => [
                'text'    => 'Connaissez-vous le montant exact de vos ventes du mois dernier ?',
                'options' => [
                    ['text' => 'Non, j\'estime à peu près', 'score' => 0],
                    ['text' => 'Approximativement', 'score' => 1],
                    ['text' => 'Oui, par une estimation calculée', 'score' => 2],
                    ['text' => 'Oui, exactement, via mon registre de ventes', 'score' => 3],
                ],
            ],
            'tresorerie' => [
                'text'    => 'Connaissez-vous le montant d\'argent disponible aujourd\'hui pour votre activité ?',
                'options' => [
                    ['text' => 'Non, je le découvre en cas de besoin', 'score' => 0],
                    ['text' => 'Je le sais seulement quand il y a un problème', 'score' => 1],
                    ['text' => 'Approximativement', 'score' => 2],
                    ['text' => 'Oui, je le suis quotidiennement', 'score' => 3],
                ],
            ],
            'budget_previsionnel' => [
                'text'    => 'Anticipez-vous vos grosses dépenses (loyer, stocks, impôts) avant de les rencontrer ?',
                'options' => [
                    ['text' => 'Non, je gère au jour le jour', 'score' => 0],
                    ['text' => 'Rarement, je découvre quand c\'est dû', 'score' => 1],
                    ['text' => 'Oui, certaines dépenses importantes', 'score' => 2],
                    ['text' => 'Oui, je planifie mes échéances à l\'avance', 'score' => 3],
                ],
            ],
            'separation_taches' => [
                'text'    => 'La personne qui encaisse l\'argent est-elle différente de celle qui vérifie les comptes ?',
                'options' => [
                    ['text' => 'Non, une seule personne (moi) fait tout', 'score' => 0],
                    ['text' => 'Non, mais j\'ai conscience du risque', 'score' => 1],
                    ['text' => 'En partie, certaines tâches sont partagées', 'score' => 2],
                    ['text' => 'Oui, les rôles sont bien répartis', 'score' => 3],
                ],
            ],
            'credit_endettement' => [
                'text'    => 'Connaissez-vous le total exact de vos dettes et échéances à venir ?',
                'options' => [
                    ['text' => 'Non, je découvre quand on me réclame', 'score' => 0],
                    ['text' => 'Approximativement', 'score' => 1],
                    ['text' => 'Oui, mais je ne les planifie pas', 'score' => 2],
                    ['text' => 'Oui, avec un calendrier de remboursement', 'score' => 3],
                ],
            ],
            'facturation_recouvrement' => [
                'text'    => 'Vos clients vous paient-ils dans un délai que vous maîtrisez ?',
                'options' => [
                    ['text' => 'Non, les retards sont fréquents et subis', 'score' => 0],
                    ['text' => 'Parfois, selon les clients', 'score' => 1],
                    ['text' => 'Souvent, avec quelques relances', 'score' => 2],
                    ['text' => 'Oui, mes délais de paiement sont tenus', 'score' => 3],
                ],
            ],
            'pilotage_decisions' => [
                'text'    => 'Utilisez-vous vos chiffres (ventes, dépenses) pour prendre vos décisions ?',
                'options' => [
                    ['text' => 'Non, je décide à l\'instinct', 'score' => 0],
                    ['text' => 'Rarement, pour les grosses décisions seulement', 'score' => 1],
                    ['text' => 'Parfois, quand j\'ai les chiffres sous la main', 'score' => 2],
                    ['text' => 'Oui, mes chiffres guident mes choix', 'score' => 3],
                ],
            ],
        ];

        foreach ($questions as $code => $data) {
            $dimension = Dimension::where('code', $code)->firstOrFail();

            $question = Question::updateOrCreate(
                ['dimension_id' => $dimension->id],
                [
                    'text'          => $data['text'],
                    'display_order' => $dimension->display_order,
                ]
            );

            foreach ($data['options'] as $index => $option) {
                $question->options()->updateOrCreate(
                    ['text' => $option['text']],
                    [
                        'score'         => $option['score'],
                        'display_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
