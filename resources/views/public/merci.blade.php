<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message envoyé — SmartSuggest QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center px-4">

<div class="max-w-md mx-auto text-center">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        {{-- Icône succès --}}
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Merci pour votre contribution !</h1>
        <p class="text-gray-500 mb-6">Votre message a bien été enregistré.</p>

        {{-- Référence --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <p class="text-sm text-gray-500 mb-1">Votre référence</p>
            <p class="text-2xl font-bold text-blue-600 tracking-widest">{{ $suggestion->reference }}</p>
            <p class="text-xs text-gray-400 mt-1">Conservez cette référence pour le suivi de votre demande</p>
        </div>

        {{-- Détails --}}
        <div class="text-left space-y-2 mb-6">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Service</span>
                <span class="font-medium text-gray-800">{{ $suggestion->service->nom }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Type</span>
                <span class="font-medium text-gray-800">{{ ucfirst($suggestion->type) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Date</span>
                <span class="font-medium text-gray-800">{{ $suggestion->created_at->format('d/m/Y à H:i') }}</span>
            </div>
            @if($suggestion->priorite === 'haute')
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Priorité</span>
                    <span class="font-medium text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>
                        Haute priorité — traitement urgent
                    </span>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('suggestion.suivi.resultat', $suggestion->reference) }}"
               class="inline-flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Suivre ce message
            </a>
            <a href="{{ route('suggestion.form') }}"
               class="inline-flex items-center justify-center gap-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Soumettre un autre message
            </a>
        </div>
    </div>
</div>

</body>
</html>
