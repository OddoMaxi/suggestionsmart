<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport SmartSuggest QR — {{ $periode }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 20px; }
        h1 { color: #1d4ed8; font-size: 18px; margin-bottom: 4px; }
        h2 { color: #374151; font-size: 13px; margin: 16px 0 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .entete { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-warn    { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-gray    { background: #f3f4f6; color: #374151; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 16px; }
        .stat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; text-align: center; }
        .stat-box .val { font-size: 22px; font-weight: bold; color: #1d4ed8; }
        .stat-box .lab { font-size: 10px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        th { background: #1d4ed8; color: white; padding: 5px 8px; text-align: left; }
        td { padding: 4px 8px; border-bottom: 1px solid #f3f4f6; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; text-align: center; color: #9ca3af; font-size: 9px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .haute { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>

<div class="entete">
    <div>
        <h1>SmartSuggest QR — Rapport {{ $typeRapport }}</h1>
        <p>Période : {{ $periode }} | Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        @if($orgNom)
            <p>Organisation : <strong>{{ $orgNom }}</strong></p>
        @endif
    </div>
</div>

<h2>Statistiques globales</h2>
<div class="stats-grid">
    <div class="stat-box">
        <div class="val">{{ $stats['total'] }}</div>
        <div class="lab">Total messages</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#3b82f6">{{ $stats['suggestions'] }}</div>
        <div class="lab">Suggestions</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#f59e0b">{{ $stats['critiques'] }}</div>
        <div class="lab">Critiques</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#22c55e">{{ $stats['felicitations'] }}</div>
        <div class="lab">Félicitations</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#dc2626">{{ $stats['haute_priorite'] }}</div>
        <div class="lab">Haute priorité</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#6366f1">{{ $stats['traites'] }}</div>
        <div class="lab">Traités</div>
    </div>
    <div class="stat-box">
        <div class="val" style="color:#f59e0b">
            {{ $stats['total'] > 0 ? number_format($stats['satisfaction_moy'], 1) : '—' }}
        </div>
        <div class="lab">Satisfaction moy.</div>
    </div>
</div>

<h2>Détail des messages ({{ $suggestions->count() }})</h2>
<table>
    <thead>
        <tr>
            <th>Référence</th>
            <th>Agence</th>
            <th>Service</th>
            <th>Type</th>
            <th>Auteur</th>
            <th>Statut</th>
            <th>Priorité</th>
            <th>Note</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suggestions as $s)
        <tr>
            <td><strong>{{ $s->reference }}</strong></td>
            <td>{{ $s->agence?->nom ?? '—' }}</td>
            <td>{{ $s->service?->nom }}</td>
            <td>
                <span class="badge badge-{{ ['suggestion'=>'info','critique'=>'warn','felicitation'=>'success'][$s->type] ?? 'gray' }}">
                    {{ ucfirst($s->type) }}
                </span>
            </td>
            <td>{{ $s->nom_affichage }}</td>
            <td>{{ ucfirst($s->statut) }}</td>
            <td class="{{ $s->priorite === 'haute' ? 'haute' : '' }}">{{ ucfirst($s->priorite) }}</td>
            <td>{{ $s->satisfaction ? str_repeat('★', $s->satisfaction) : '—' }}</td>
            <td>{{ $s->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    SmartSuggest QR | Rapport généré automatiquement | {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
