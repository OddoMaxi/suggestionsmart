<?php

use Illuminate\Support\Facades\Schedule;

// Nettoyage des logs activité (garder 6 mois)
Schedule::command('activitylog:clean')->monthly();

// Nettoyage des sessions expirées
Schedule::command('session:gc')->daily();

// Export automatique mensuel (optionnel)
// Schedule::command('smartsuggest:rapport-mensuel')->monthlyOn(1, '08:00');
