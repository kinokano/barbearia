<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration_minutes',
        'price',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price'  => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(Professional::class, 'professional_service')
                    ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
