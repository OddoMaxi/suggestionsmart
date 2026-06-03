<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limite atteinte — SmartSuggest QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 min-h-screen flex items-center justify-center px-4">
<div class="max-w-sm mx-auto text-center bg-white rounded-2xl shadow-xl p-8">
    <div class="text-5xl mb-4">⏳</div>
    <h1 class="text-xl font-bold text-gray-800 mb-2">Trop de tentatives</h1>
    <p class="text-gray-500 mb-4">
        Vous avez soumis trop de messages en peu de temps.<br>
        Veuillez patienter <strong>{{ $minutes }} minute{{ $minutes > 1 ? 's' : '' }}</strong> avant de réessayer.
    </p>
    <a href="{{ route('suggestion.form') }}" class="text-blue-600 hover:underline text-sm">Retour au formulaire</a>
</div>
</body>
</html>
