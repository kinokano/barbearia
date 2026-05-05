<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            User::create([
                'nome' => 'Administrador',
                'email' => 'admin@turetta.com',
                'password' => Hash::make('admin123'),
                'telefone' => '00000000000'
            ]);
        }
        Professional::insert([
            ['nome' => 'Carlos Turetta', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Rafael', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Lucas', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Service::insert([
            ['nome' => 'Corte Masculino', 'descricao' => 'Corte na máquina e tesoura', 'preco' => 45.00, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Barba', 'descricao' => 'Barba na navalha com toalha quente', 'preco' => 35.00, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Corte + Barba', 'descricao' => 'Combo corte e barba completa', 'preco' => 70.00, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Pigmentação', 'descricao' => 'Pigmentação capilar', 'preco' => 80.00, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Sobrancelha', 'descricao' => 'Design de sobrancelha masculina', 'preco' => 20.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
