<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Agenda global da barbearia com filtro de data.
     */
    public function index(Request $request): View
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->format('Y-m-d')
            : now()->format('Y-m-d');

        $appointments = Appointment::with(['professional.user', 'service'])
            ->whereDate('date', $date)
            ->orderBy('start_time')
            ->get();

        $professionals = Professional::with('user')->active()->get();

        $pendingCount = Appointment::where('status', 'pendente')->count();

        return view('admin.dashboard', compact('appointments', 'professionals', 'date', 'pendingCount'));
    }

    /**
     * Atualiza o status de um agendamento.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pendente,agendado,cancelado'],
        ]);

        $appointment->update($validated);

        return back()->with('success', 'Status do agendamento atualizado.');
    }

    /**
     * Exclui permanentemente um agendamento.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        $date = $appointment->date->format('Y-m-d');
        $appointment->delete();

        return redirect()
            ->route('admin.dashboard', ['date' => $date])
            ->with('success', 'Agendamento excluído com sucesso.');
    }
}

