<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'org_nom',             'value' => 'Mon Organisation',          'type' => 'string',  'groupe' => 'general',        'label' => 'Nom de l\'organisation'],
            ['key' => 'org_telephone',       'value' => '',                           'type' => 'string',  'groupe' => 'general',        'label' => 'Téléphone'],
            ['key' => 'org_adresse',         'value' => '',                           'type' => 'string',  'groupe' => 'general',        'label' => 'Adresse'],
            ['key' => 'org_logo',            'value' => '',                           'type' => 'file',    'groupe' => 'general',        'label' => 'Logo'],
            ['key' => 'email_notification',  'value' => 'admin@smartsuggest.local',   'type' => 'string',  'groupe' => 'notifications',  'label' => 'Email notifications'],
            ['key' => 'notif_admin_actif',   'value' => '1',                          'type' => 'boolean', 'groupe' => 'notifications',  'label' => 'Notifier l\'admin'],
            ['key' => 'notif_resp_actif',    'value' => '1',                          'type' => 'boolean', 'groupe' => 'notifications',  'label' => 'Notifier le responsable'],
            ['key' => 'max_par_heure',       'value' => '10',                         'type' => 'integer', 'groupe' => 'securite',       'label' => 'Max soumissions/heure'],
            ['key' => 'couleur_principale',  'value' => '#3B82F6',                    'type' => 'string',  'groupe' => 'apparence',      'label' => 'Couleur principale'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
