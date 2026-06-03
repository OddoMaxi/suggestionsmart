<?php

namespace App\Exports;

use App\Models\Suggestion;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuggestionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        private readonly ?string $dateDebut = null,
        private readonly ?string $dateFin = null,
        private readonly ?string $serviceId = null,
        private readonly ?string $type = null,
        private readonly ?string $statut = null,
    ) {}

    public function query(): Builder
    {
        return Suggestion::with(['service', 'agence'])
            ->when($this->dateDebut, fn ($q) => $q->whereDate('created_at', '>=', $this->dateDebut))
            ->when($this->dateFin,   fn ($q) => $q->whereDate('created_at', '<=', $this->dateFin))
            ->when($this->serviceId, fn ($q) => $q->where('service_id', $this->serviceId))
            ->when($this->type,      fn ($q) => $q->where('type', $this->type))
            ->when($this->statut,    fn ($q) => $q->where('statut', $this->statut))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Nom',
            'Prénom',
            'Téléphone',
            'Email',
            'Agence',
            'Service',
            'Type',
            'Statut',
            'Priorité',
            'Satisfaction (1-5)',
            'Message',
            'Date de soumission',
        ];
    }

    public function map($suggestion): array
    {
        return [
            $suggestion->reference,
            $suggestion->nom,
            $suggestion->prenom,
            $suggestion->telephone,
            $suggestion->email ?? '',
            $suggestion->agence?->nom ?? '',
            $suggestion->service?->nom ?? '',
            ucfirst($suggestion->type),
            ucfirst($suggestion->statut),
            ucfirst($suggestion->priorite),
            $suggestion->satisfaction ?? '',
            $suggestion->message,
            $suggestion->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF3B82F6']]],
        ];
    }
}
