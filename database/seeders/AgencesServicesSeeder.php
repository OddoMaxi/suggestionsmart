<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgencesServicesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nom'         => 'Cabinet du Ministre',
                'slug'        => 'cabinet-du-ministre',
                'description' => 'Cabinet du Ministre de la Sécurité et de la Protection Civile',
                'services'    => [
                    // Services rattachés
                    'Service de Coordination des Brigades Anti-Criminalité',
                    'Antenne Nationale de l\'Institut Africain des Nations Unies pour la Prévention du Crime et le Traitement des Délinquants',
                    'Bureau National de Liaison AFRIPOL',
                    'Office Central Anti-Drogue',
                    'Office de Répression des Délits Économiques et Financiers',
                    // Directions Générales
                    'Direction Générale de la Police Nationale',
                    'Direction Générale du Renseignement Intérieur',
                    'Direction Générale de la Protection Civile',
                    'Direction Générale du Service de Santé de la Police et de la Protection Civile',
                    // Services d'appui
                    'Inspection Générale',
                    'Bureau de Stratégie et de Développement',
                    'Bureau des Droits de l\'Homme et de Droit International Humanitaire',
                    'Direction des Ressources Humaines',
                    'Division des Affaires Financières (DAF)',
                    'Contrôleur Financier',
                    'Personne Responsable des Marchés Publics',
                    'Service Genre et Équité',
                    'Service de Modernisation des Systèmes d\'Information',
                    'Service de la Coopération et des Relations Extérieures',
                    'Service de Communication et des Relations Publiques',
                    'Secrétariat Central (SC)',
                    // EPA
                    'Office de Protection du Genre, de l\'Enfance et des Mœurs (OPROGEM)',
                    'Fonds Social de la Police et de la Protection Civile',
                    'École Nationale de la Police et de la Protection Civile',
                    'Office de Régulation des Agences de Sécurité et de Protection Civile',
                    'Autorité Nationale de la Cybersécurité et des Titres Sécurisés',
                    'Office National d\'Identification',
                ],
            ],
            [
                'nom'         => 'Direction Générale de la Police Nationale (DGPN)',
                'slug'        => 'dgpn',
                'description' => 'Direction Générale de la Police Nationale',
                'services'    => [
                    'Groupement d\'Intervention et de Protection de la Police Nationale (GIPPN)',
                    'Brigades Anti-Criminalité (BAC)',
                    'Direction de la Cybercriminalité',
                    'Direction Centrale des Compagnies Mobiles d\'Intervention et de Sécurité',
                    'Direction Centrale de la Police Judiciaire',
                    'Direction Centrale de la Police Aux Frontières',
                    'Direction Centrale de la Sécurité Publique',
                    'Direction Centrale de la Sécurité Routière',
                    'Direction Régionale de la Police de Conakry',
                    'Direction Régionale de la Police de Kindia',
                    'Direction Régionale de la Police de Boké',
                    'Direction Régionale de la Police de Mamou',
                    'Direction Régionale de la Police de Labé',
                    'Direction Régionale de la Police de Faranah',
                    'Direction Régionale de la Police de Kankan',
                    'Direction Régionale de la Police de N\'Zérékoré',
                ],
            ],
            [
                'nom'         => 'Direction Générale de la Protection Civile (DGPC)',
                'slug'        => 'dgpc',
                'description' => 'Direction Générale de la Protection Civile',
                'services'    => [
                    'Caserne Principale de Tombo (Kaloum)',
                    'Caserne de Ratoma (Kaporo / Nongo)',
                    'Caserne de Matoto',
                    'Caserne de Dixinn',
                    'Antenne Mobile d\'Intervention d\'Enco 5 (Ratoma)',
                    'Unité de Protection Civile de Manéah',
                    'UPC de Boké',
                    'UPC de Kankan',
                    'UPC de Nzérékoré',
                    'Caserne de Kindia',
                    'Caserne de Labé',
                ],
            ],
        ];

        foreach ($data as $agenceData) {
            $services = $agenceData['services'];
            unset($agenceData['services']);

            $agenceId = Str::uuid()->toString();
            $now = now();

            DB::table('agences')->insert([
                'id'          => $agenceId,
                'nom'         => $agenceData['nom'],
                'slug'        => $agenceData['slug'],
                'description' => $agenceData['description'],
                'actif'       => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            foreach ($services as $ordre => $serviceNom) {
                $baseSlug = Str::slug($serviceNom);
                $slug = $baseSlug;
                $i = 1;
                while (DB::table('services')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $i++;
                }

                DB::table('services')->insert([
                    'id'         => Str::uuid()->toString(),
                    'agence_id'  => $agenceId,
                    'nom'        => $serviceNom,
                    'slug'       => $slug,
                    'actif'      => true,
                    'ordre'      => $ordre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
