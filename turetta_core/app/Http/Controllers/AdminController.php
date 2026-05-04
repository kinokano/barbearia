<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function appointments(Request $request)
    {
        $query = Appointment::with(['user', 'professional', 'service']);

        if ($request->has('data')) {
            $query->where('data_agendamento', $request->data);
        }

        if ($request->has('data_inicio') && $request->has('data_fim')) {
            $query->whereBetween('data_agendamento', [$request->data_inicio, $request->data_fim]);
        }

        $appointments = $query->orderBy('data_agendamento')
            ->orderBy('horario_agendamento')
            ->get();

        return response()->json($appointments);
    }

    public function clients()
    {
        $clients = User::withCount('appointments')
            ->orderBy('nome')
            ->get();

        return response()->json($clients);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:agendado,confirmado,concluido,cancelado',
        ]);

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status atualizado.',
            'appointment' => $appointment->load(['user', 'professional', 'service']),
        ]);
    }
}
