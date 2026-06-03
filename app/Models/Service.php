<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'agence_id', 'nom', 'slug', 'description',
        'responsable', 'email_responsable', 'telephone_responsable',
        'actif', 'couleur', 'icone', 'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->nom);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('service');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }

    public function qrCodeUrl(): string
    {
        return route('suggestion.form') . '?service=' . $this->slug
            . ($this->agence ? '&agence=' . $this->agence->slug : '');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
