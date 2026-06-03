<?php

namespace App\Models;

use App\Services\PrioriteService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Suggestion extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference', 'service_id', 'agence_id',
        'nom', 'prenom', 'telephone', 'email',
        'type', 'message', 'satisfaction',
        'statut', 'priorite', 'assigne_a',
        'commentaire_interne', 'ip_address', 'user_agent', 'canal',
    ];

    protected $casts = [
        'satisfaction' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['statut', 'priorite', 'assigne_a', 'commentaire_interne'])
            ->useLogName('suggestion');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $suggestion) {
            $suggestion->reference = self::genererReference();
            $suggestion->priorite = app(PrioriteService::class)->evaluer($suggestion->message);
        });
    }

    private static function genererReference(): string
    {
        $annee = now()->format('Y');
        $dernier = self::whereYear('created_at', $annee)->count() + 1;
        return 'SS-' . $annee . '-' . str_pad($dernier, 6, '0', STR_PAD_LEFT);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function assigneA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_a');
    }

    public function getBadgePrioriteAttribute(): array
    {
        return $this->priorite === 'haute'
            ? ['color' => 'danger', 'label' => 'Haute']
            : ['color' => 'gray', 'label' => 'Normale'];
    }

    public function getBadgeStatutAttribute(): array
    {
        return match ($this->statut) {
            'nouveau'  => ['color' => 'info', 'label' => 'Nouveau'],
            'en_cours' => ['color' => 'warning', 'label' => 'En cours'],
            'traite'   => ['color' => 'success', 'label' => 'Traité'],
            'cloture'  => ['color' => 'gray', 'label' => 'Clôturé'],
            default    => ['color' => 'gray', 'label' => $this->statut],
        };
    }

    public function getBadgeTypeAttribute(): array
    {
        return match ($this->type) {
            'suggestion'   => ['color' => 'info', 'label' => 'Suggestion', 'icon' => 'heroicon-o-light-bulb'],
            'critique'     => ['color' => 'warning', 'label' => 'Critique', 'icon' => 'heroicon-o-exclamation-triangle'],
            'reclamation'  => ['color' => 'danger', 'label' => 'Réclamation', 'icon' => 'heroicon-o-x-circle'],
            'felicitation' => ['color' => 'success', 'label' => 'Félicitation', 'icon' => 'heroicon-o-star'],
            default        => ['color' => 'gray', 'label' => $this->type, 'icon' => 'heroicon-o-chat-bubble-left'],
        };
    }

    public function scopeNouveau($query) { return $query->where('statut', 'nouveau'); }
    public function scopeHautePriorite($query) { return $query->where('priorite', 'haute'); }
    public function scopeParType($query, string $type) { return $query->where('type', $type); }
    public function scopeParService($query, string $serviceId) { return $query->where('service_id', $serviceId); }
    public function scopeParAgence($query, string $agenceId) { return $query->where('agence_id', $agenceId); }
}
