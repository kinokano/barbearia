<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['nome', 'data_nascimento', 'telefone'];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
