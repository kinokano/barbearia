<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    /**
     * Agenda do profissional logado.
     */
    public function index(Request $request): View
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $professional = Professional::where('user_id', auth()->id())->firstOrFail();

        $appointments = Appointment::with('service')
            ->where('professional_id', $professional->id)
            ->where('date', $date)
            ->orderBy('start_time')
            ->get();

        return view('professional.agenda', compact('appointments', 'date', 'professional'));
    }

    /**
     * Detalhes de um agendamento do profissional.
     */
    public function show(Appointment $appointment): View
    {
        $professional = Professional::where('user_id', auth()->id())->firstOrFail();

        // Garante que o profissional só vê seus próprios agendamentos
        if ($appointment->professional_id !== $professional->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $appointment->load('service');

        return view('professional.appointment-detail', compact('appointment'));
    }
}
