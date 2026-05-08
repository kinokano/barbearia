<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * CRM: Lista clientes únicos com último serviço, aniversário e link WhatsApp.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        // Agrupa por telefone (identificador do cliente público)
        $clientsQuery = Appointment::query()
            ->selectRaw('
                client_name,
                client_phone,
                client_birth_date,
                MAX(date) as last_service_date
            ')
            ->groupBy('client_phone', 'client_name', 'client_birth_date')
            ->orderByDesc('last_service_date');

        if ($search) {
            $clientsQuery->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_phone', 'like', "%{$search}%");
            });
        }

        $clients = $clientsQuery->paginate(20);

        return view('admin.clients', compact('clients', 'search'));
    }

    /**
     * Atualiza o status de um agendamento (pendente, agendado, cancelado).
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pendente,agendado,cancelado'],
        ]);

        $appointment->update($validated);

        return back()->with('success', 'Status atualizado com sucesso.');
    }
}
