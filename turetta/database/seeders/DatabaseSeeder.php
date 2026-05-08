<?php

namespace Database\Seeders;

use App\Models\Professional;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin        = Role::create(['name' => 'admin']);
        $professional = Role::create(['name' => 'professional']);
        $client       = Role::create(['name' => 'client']);

        // Admin padrão
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@turetta.com',
            'password' => Hash::make('turetta2026'),
            'phone'    => '11999999999',
            'role_id'  => $admin->id,
        ]);

        // Serviços
        $corte = Service::create([
            'name'             => 'Corte',
            'description'      => 'Corte de cabelo masculino',
            'duration_minutes' => 30,
            'price'            => 35.00,
        ]);

        $barba = Service::create([
            'name'             => 'Barba',
            'description'      => 'Barba completa com toalha quente',
            'duration_minutes' => 20,
            'price'            => 25.00,
        ]);

        $corteBarba = Service::create([
            'name'             => 'Corte e Barba',
            'description'      => 'Combo corte de cabelo + barba',
            'duration_minutes' => 45,
            'price'            => 55.00,
        ]);

        // Profissional de exemplo
        $profUser = User::create([
            'name'     => 'João Barbeiro',
            'email'    => 'joao@turetta.com',
            'password' => Hash::make('turetta2026'),
            'phone'    => '11988887777',
            'role_id'  => $professional->id,
        ]);

        $prof = Professional::create([
            'user_id'   => $profUser->id,
            'specialty' => 'Corte e Barba',
            'active'    => true,
        ]);

        // Vincula todos os serviços ao profissional
        $prof->services()->attach([$corte->id, $barba->id, $corteBarba->id]);

        // Horários do profissional (Seg-Sáb, 09:00-18:00)
        for ($day = 1; $day <= 6; $day++) {
            Schedule::create([
                'professional_id' => $prof->id,
                'day_of_week'     => $day,
                'start_time'      => '09:00',
                'end_time'        => '18:00',
            ]);
        }
    }
}
