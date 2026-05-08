<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'client_name',
        'client_phone',
        'client_birth_date',
        'professional_id',
        'service_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'              => 'date',
            'client_birth_date' => 'date',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Retorna a classe CSS correspondente ao status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pendente'  => 'bg-yellow-100 text-yellow-800',
            'agendado'  => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }
}
