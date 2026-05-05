<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['nome', 'data_nascimento', 'telefone', 'email', 'password'];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
