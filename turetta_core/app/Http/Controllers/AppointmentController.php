<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'telefone' => 'required|string|max:20',
            'professional_id' => 'required|exists:professionals,id',
            'service_id' => 'required|exists:services,id',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'horario_agendamento' => 'required|date_format:H:i',
        ]);

        $user = User::firstOrCreate(
            ['telefone' => $validated['telefone']],
            [
                'nome' => $validated['nome'],
                'data_nascimento' => $validated['data_nascimento'],
            ]
        );

        $exists = Appointment::where('professional_id', $validated['professional_id'])
            ->where('data_agendamento', $validated['data_agendamento'])
            ->where('horario_agendamento', $validated['horario_agendamento'])
            ->where('status', '!=', 'cancelado')
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Horário indisponível.'], 409);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'professional_id' => $validated['professional_id'],
            'service_id' => $validated['service_id'],
            'data_agendamento' => $validated['data_agendamento'],
            'horario_agendamento' => $validated['horario_agendamento'],
            'status' => 'agendado',
        ]);

        return response()->json([
            'message' => 'Agendamento realizado com sucesso!',
            'appointment' => $appointment->load(['user', 'professional', 'service']),
        ], 201);
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'professional_id' => 'required|exists:professionals,id',
        ]);

        $allSlots = [];
        for ($h = 9; $h <= 19; $h++) {
            $allSlots[] = sprintf('%02d:00', $h);
            if ($h < 19) {
                $allSlots[] = sprintf('%02d:30', $h);
            }
        }

        $booked = Appointment::where('professional_id', $request->professional_id)
            ->where('data_agendamento', $request->data)
            ->where('status', '!=', 'cancelado')
            ->pluck('horario_agendamento')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();

        $available = array_values(array_diff($allSlots, $booked));

        return response()->json(['slots' => $available]);
    }
}
