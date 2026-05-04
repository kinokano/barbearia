<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'professional_id',
        'service_id',
        'data_agendamento',
        'horario_agendamento',
        'status',
    ];

    protected $casts = [
        'data_agendamento' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
