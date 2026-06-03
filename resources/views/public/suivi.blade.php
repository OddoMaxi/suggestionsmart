<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>Suivi de message — SmartSuggest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-10">

<div class="w-full max-w-sm">

    {{-- Logo / Titre --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl shadow-lg mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Suivi de message</h1>
        <p class="text-gray-500 text-sm mt-1">Entrez votre référence pour consulter l'état</p>
    </div>

    {{-- Formulaire de recherche --}}
    <form action="{{ route('suggestion.suivi.resultat', ':ref') }}"
          method="GET"
          onsubmit="this.action = this.action.replace(':ref', encodeURIComponent(document.getElementById('ref').value.trim().toUpperCase()))"
          class="mb-6">
        <div class="flex gap-2">
            <input id="ref" type="text" name="reference"
                   placeholder="SS-2026-000001"
                   value="{{ $reference ?? '' }}"
                   required
                   autocomplete="off"
                   autocapitalize="characters"
                   class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:outline-none text-sm font-mono tracking-widest bg-white shadow-sm">
            <button type="submit"
                    class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </form>

    {{-- Résultat --}}
    @isset($suggestion)
        @if($suggestion)
            @php
                $statutConfig = match($suggestion->statut) {
                    'nouveau'  => ['color' => 'blue',   'icon' => '🔵', 'label' => 'Nouveau',   'desc' => 'Votre message a été reçu et sera traité prochainement.'],
                    'en_cours' => ['color' => 'amber',  'icon' => '🟡', 'label' => 'En cours',  'desc' => 'Votre message est en cours de traitement.'],
                    'traite'   => ['color' => 'green',  'icon' => '🟢', 'label' => 'Traité',    'desc' => 'Votre message a été traité avec succès.'],
                    'cloture'  => ['color' => 'gray',   'icon' => '⚫', 'label' => 'Clôturé',   'desc' => 'Ce dossier a été clôturé.'],
                    default    => ['color' => 'gray',   'icon' => '⚪', 'label' => $suggestion->statut, 'desc' => ''],
                };
                $typeConfig = match($suggestion->type) {
                    'suggestion'   => ['emoji' => '💡', 'label' => 'Suggestion'],
                    'critique'     => ['emoji' => '⚠️', 'label' => 'Critique'],
                    'reclamation'  => ['emoji' => '🚨', 'label' => 'Réclamation'],
                    'felicitation' => ['emoji' => '🌟', 'label' => 'Félicitation'],
                    default        => ['emoji' => '📝', 'label' => ucfirst($suggestion->type)],
                };
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Bandeau statut --}}
                <div class="px-5 py-4 bg-{{ $statutConfig['color'] }}-50 border-b border-{{ $statutConfig['color'] }}-100">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $statutConfig['icon'] }}</span>
                        <div>
                            <p class="font-bold text-{{ $statutConfig['color'] }}-800 text-sm">{{ $statutConfig['label'] }}</p>
                            <p class="text-{{ $statutConfig['color'] }}-600 text-xs">{{ $statutConfig['desc'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Détails --}}
                <div class="px-5 py-4 space-y-3">

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Référence</span>
                        <span class="font-mono font-bold text-gray-800 text-sm">{{ $suggestion->reference }}</span>
                    </div>

                    <div class="h-px bg-gray-50"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Type</span>
                        <span class="text-sm font-semibold text-gray-700">
                            {{ $typeConfig['emoji'] }} {{ $typeConfig['label'] }}
                        </span>
                    </div>

                    <div class="h-px bg-gray-50"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Service</span>
                        <span class="text-sm font-semibold text-gray-700 text-right max-w-[180px]">
                            {{ $suggestion->service->nom }}
                        </span>
                    </div>

                    @if($suggestion->agence)
                    <div class="h-px bg-gray-50"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Agence</span>
                        <span class="text-sm text-gray-600">{{ $suggestion->agence->nom }}</span>
                    </div>
                    @endif

                    <div class="h-px bg-gray-50"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Soumis le</span>
                        <span class="text-sm text-gray-600">{{ $suggestion->created_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    @if($suggestion->updated_at->ne($suggestion->created_at))
                    <div class="h-px bg-gray-50"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Mis à jour</span>
                        <span class="text-sm text-gray-600">{{ $suggestion->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @endif

                </div>
            </div>

        @else
            {{-- Référence introuvable --}}
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6 text-center">
                <div class="text-4xl mb-3">🔍</div>
                <p class="font-semibold text-gray-800 mb-1">Référence introuvable</p>
                <p class="text-sm text-gray-500">
                    Vérifiez que vous avez saisi correctement la référence.<br>
                    Exemple : <span class="font-mono text-indigo-600">SS-2026-000001</span>
                </p>
            </div>
        @endif
    @endisset

    {{-- Liens bas de page --}}
    <div class="mt-6 flex justify-center gap-5 text-sm">
        <a href="{{ route('suggestion.form') }}" class="text-indigo-600 hover:underline font-medium">
            + Nouveau message
        </a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('suggestion.suivi') }}" class="text-gray-400 hover:text-gray-600">
            Nouvelle recherche
        </a>
    </div>

</div>

</body>
</html>
