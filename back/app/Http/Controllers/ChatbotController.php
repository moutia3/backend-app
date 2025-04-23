<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function getQuestions()
    {
        $faqs = [
            [
                'question' => 'Combien de jours par mois puis-je demander à télétravailler ?',
                'answer' => 'La politique actuelle permet jusqu\'à 8 jours de télétravail par mois, sous réserve d\'approbation de votre manager allez dans l\'onglet calendrier pour voir les jours disponibles, bloqués et limités.'
            ],
            [
                'question' => 'Quels jours fériés y a-t-il cette année ?',
                'answer' => 'Les jours fériés pour cette année sont: 1er janvier, 1er mai, 8 mai, 14 juillet, 15 août, 1er novembre, 11 novembre, et 25 décembre.'
            ],
            [
                'question' => 'Comment déposer une demande de télétravail ?',
                'answer' => 'Pour soumettre une demande, allez dans l\'onglet "Liste Des Demandes" et cliquez sur "Ajouter Une demande". Remplissez le formulaire avec la date et la raison, puis soumettez-la pour approbation.'
            ],
            [
                'question' => 'Puis-je modifier une demande soumise ?',
                'answer' => 'Oui, vous pouvez modifier une demande tant qu\'elle n\'a pas encore été approuvée ou rejetée. Allez dans "Liste Des Demandes", sélectionnez la demande et cliquez sur "Modifier".'
            ],
            [
                'question' => 'Quand serai-je informé de la décision sur ma demande ?',
                'answer' => 'Les demandes sont généralement traitées dans un délai de 2 à 3 jours ouvrables. Vous recevrez une notification par email et dans l\'application une fois la décision prise.'
            ]
        ];

        return response()->json($faqs);
    }

    public function getAnswer(Request $request)
    {
        $question = $request->input('question');
        
        $faqs = [
            'Combien de jours par mois puis-je demander à télétravailler ?' => 
                'La politique actuelle permet jusqu\'à 8 jours de télétravail par mois, sous réserve d\'approbation de votre manager allez dans l\'onglet Calendrier De Télétravail pour voir les jours disponibles, bloqués et limités.',
            'Quels jours fériés y a-t-il cette année ?' => 
                                'Les jours fériés pour cette année sont:
                Jeudi 20 mars : Fête de l’Indépendance (commémore l’indépendance de la Tunisie en 1956)
                Mercredi 9 avril : Journée des Martyrs (en mémoire des événements du 9 avril 1938)
                Jeudi 1er mai : Fête du Travail
                Vendredi 25 juillet : Fête de la République (proclamation de la République tunisienne en 1957)
                Mercredi 13 août : Fête de la Femme (promulgation du Code du Statut Personnel en 1956)
                Mercredi 15 octobre : Fête de l’Évacuation (commémore le départ des dernières troupes françaises en 1963)
                Mercredi 17 décembre : Fête de la Révolution (marque le déclenchement de la révolution tunisienne en 2010)',
            'Comment déposer une demande de télétravail ?' => 
                'Pour soumettre une demande, allez dans l\'onglet "Liste Des Demandes" et cliquez sur "Ajouter Une demande". Remplissez le formulaire avec la date et la raison, puis soumettez-la pour approbation.',
            'Puis-je modifier une demande soumise ?' => 
                'Oui, vous pouvez modifier une demande tant qu\'elle n\'a pas encore été approuvée ou rejetée. Allez dans "Liste Des Demandes", sélectionnez la demande et cliquez sur "Modifier".',
            'Quand serai-je informé de la décision sur ma demande ?' => 
                'Les demandes sont généralement traitées dans un délai de 2 à 3 jours ouvrables. Vous recevrez une notification par email et dans l\'application une fois la décision prise.'
        ];

        $answer = $faqs[$question] ?? 'Désolé, je n\'ai pas d\'information sur cette question. Veuillez contacter le service RH pour plus d\'aide.';

        return response()->json(['answer' => $answer]);
    }
}