<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Step 1: Escolher serviço.
     */
    public function selectService(): View
    {
        $services = Service::active()->orderBy('name')->get();

        return view('client.booking', [
            'step'     => 1,
            'services' => $services,
        ]);
    }

    /**
     * Step 2: Escolher profissional (que oferece o serviço selecionado).
     */
    public function selectProfessional(Service $service): View
    {
        $professionals = $service->professionals()
            ->where('active', true)
            ->with('user')
            ->get();

        return view('client.booking', [
            'step'          => 2,
            'service'       => $service,
            'professionals' => $professionals,
        ]);
    }

    /**
     * Step 3: Escolher data e horário.
     */
    public function selectSlot(Service $service, Professional $professional): View
    {
        return view('client.booking', [
            'step'         => 3,
            'service'      => $service,
            'professional' => $professional,
        ]);
    }

    /**
     * AJAX: Retorna os horários disponíveis de um profissional em uma data.
     *
     * Lógica:
     * 1. Busca o Schedule do profissional para o day_of_week da data
     * 2. Gera slots de 30 em 30 min dentro do horário de trabalho
     *    (cada slot tem duração = duração do serviço)
     * 3. Busca appointments existentes para aquele profissional + data (não cancelados)
     * 4. Remove qualquer slot cujo período [start, start+duração] colida com um agendamento
     * 5. Retorna apenas slots disponíveis
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date'            => ['required', 'date', 'after_or_equal:today'],
            'professional_id' => ['required', 'exists:professionals,id'],
            'service_id'      => ['required', 'exists:services,id'],
        ]);

        $date           = Carbon::parse($request->input('date'));
        $professionalId = (int) $request->input('professional_id');
        $service        = Service::findOrFail($request->input('service_id'));
        $dayOfWeek      = $date->dayOfWeek; // 0 = Domingo

        // 1. Busca o horário de trabalho do profissional naquele dia
        $schedule = Schedule::where('professional_id', $professionalId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $schedule) {
            return response()->json([
                'slots'   => [],
                'message' => 'O profissional não atende neste dia.',
            ]);
        }

        // 2. Gera slots de 30 em 30 min; cada slot tem janela = duração do serviço
        $cursor   = Carbon::parse($schedule->start_time);
        $endTime  = Carbon::parse($schedule->end_time);
        $duration = $service->duration_minutes;
        $allSlots = [];

        while ($cursor->copy()->addMinutes($duration)->lte($endTime)) {
            $allSlots[] = [
                'start' => $cursor->format('H:i'),
                'end'   => $cursor->copy()->addMinutes($duration)->format('H:i'),
            ];
            $cursor->addMinutes(30); // passo fixo de 30 minutos
        }

        // 3. Busca agendamentos existentes (não cancelados) com whereDate para garantir match correto
        $existingAppointments = Appointment::where('professional_id', $professionalId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelado')
            ->get(['start_time', 'end_time']);

        // 4. Filtra slots que colidem com qualquer agendamento existente
        //    Colisão: slot.start < appt.end  E  slot.end > appt.start
        $availableSlots = collect($allSlots)->filter(function ($slot) use ($existingAppointments) {
            $slotStart = Carbon::parse($slot['start']);
            $slotEnd   = Carbon::parse($slot['end']);

            foreach ($existingAppointments as $appointment) {
                $apptStart = Carbon::parse($appointment->start_time);
                $apptEnd   = Carbon::parse($appointment->end_time);

                if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                    return false; // Colisão detectada → slot indisponível
                }
            }

            return true;
        })->values();

        return response()->json(['slots' => $availableSlots]);
    }

    /**
     * Step 4: Formulário de confirmação (dados do cliente).
     */
    public function confirm(Request $request): View
    {
        $request->validate([
            'service_id'      => ['required', 'exists:services,id'],
            'professional_id' => ['required', 'exists:professionals,id'],
            'date'            => ['required', 'date'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i'],
        ]);

        $service      = Service::findOrFail($request->input('service_id'));
        $professional = Professional::with('user')->findOrFail($request->input('professional_id'));

        return view('client.booking', [
            'step'         => 4,
            'service'      => $service,
            'professional' => $professional,
            'date'         => $request->input('date'),
            'start_time'   => $request->input('start_time'),
            'end_time'     => $request->input('end_time'),
        ]);
    }

    /**
     * Salva o agendamento.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'       => ['required', 'exists:services,id'],
            'professional_id'  => ['required', 'exists:professionals,id'],
            'date'             => ['required', 'date', 'after_or_equal:today'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i'],
            'client_name'      => ['required', 'string', 'max:255'],
            'client_phone'     => ['required', 'string', 'max:20'],
            'client_birth_date' => ['nullable', 'date'],
        ]);

        // Verifica novamente se o slot está disponível (proteção contra race condition)
        $conflict = Appointment::where('professional_id', $validated['professional_id'])
            ->whereDate('date', $validated['date'])   // whereDate garante match correto
            ->where('status', '!=', 'cancelado')
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()
                ->withErrors(['start_time' => 'Este horário já foi reservado. Por favor, escolha outro.']);
        }

        Appointment::create([
            'client_name'       => $validated['client_name'],
            'client_phone'      => $validated['client_phone'],
            'client_birth_date' => $validated['client_birth_date'] ?? null,
            'professional_id'   => $validated['professional_id'],
            'service_id'        => $validated['service_id'],
            'date'              => $validated['date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'status'            => 'pendente',
        ]);

        return redirect()->route('client.booking.success');
    }

    /**
     * Página de sucesso.
     */
    public function success(): View
    {
        return view('client.success');
    }
}
