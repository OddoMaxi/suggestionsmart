<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Agence extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nom', 'slug', 'description', 'ville', 'adresse',
        'telephone', 'email', 'actif', 'logo', 'meta',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'meta' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $agence) {
            if (empty($agence->slug)) {
                $agence->slug = Str::slug($agence->nom);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('agence');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
