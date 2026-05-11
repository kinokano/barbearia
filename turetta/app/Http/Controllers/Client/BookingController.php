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
        $weekOffset = (int) request('week', 0);
        $today      = Carbon::today();
        $weekStart  = $today->copy()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd    = $weekStart->copy()->addDays(6);

        if ($weekStart->lt($today)) {
            $weekStart = $today;
        }

        $scheduledDays = Schedule::where('professional_id', $professional->id)
            ->pluck('day_of_week')
            ->toArray();

        $days = [];
        $cursor = $weekStart->copy();
        $realWeekStart = $today->copy()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        while ($cursor->lte($weekEnd)) {
            $days[] = [
                'date'      => $cursor->format('Y-m-d'),
                'day'       => $cursor->day,
                'label'     => mb_strtoupper(mb_substr($cursor->locale('pt_BR')->dayName, 0, 3)),
                'available' => in_array($cursor->dayOfWeek, $scheduledDays) && $cursor->gte($today),
                'past'      => $cursor->lt($today),
            ];
            $cursor->addDay();
        }

        $paddedDays = [];
        $firstDayOfWeek = $realWeekStart->copy();
        while ($firstDayOfWeek->lt($weekStart)) {
            $paddedDays[] = [
                'date'      => $firstDayOfWeek->format('Y-m-d'),
                'day'       => $firstDayOfWeek->day,
                'label'     => mb_strtoupper(mb_substr($firstDayOfWeek->locale('pt_BR')->dayName, 0, 3)),
                'available' => false,
                'past'      => true,
            ];
            $firstDayOfWeek->addDay();
        }
        $days = array_merge($paddedDays, $days);

        return view('client.booking', [
            'step'         => 3,
            'service'      => $service,
            'professional' => $professional,
            'days'         => $days,
            'weekOffset'   => $weekOffset,
            'weekLabel'    => $realWeekStart->format('d') . ' ' . mb_strtoupper(mb_substr($realWeekStart->locale('pt_BR')->monthName, 0, 3)) . ' — ' . $weekEnd->format('d') . ' ' . mb_strtoupper(mb_substr($weekEnd->locale('pt_BR')->monthName, 0, 3)),
            'canGoPrev'    => $weekOffset > 0,
        ]);
    }

    /**
     * AJAX: Retorna todos os horários de um profissional em uma data,
     * marcando cada um como disponível ou ocupado.
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
        $dayOfWeek      = $date->dayOfWeek;

        $schedule = Schedule::where('professional_id', $professionalId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $schedule) {
            return response()->json([
                'slots'   => [],
                'message' => 'O profissional não atende neste dia.',
            ]);
        }

        $cursor   = Carbon::parse($schedule->start_time);
        $endTime  = Carbon::parse($schedule->end_time);
        $duration = $service->duration_minutes;
        $allSlots = [];

        while ($cursor->copy()->addMinutes($duration)->lte($endTime)) {
            $allSlots[] = [
                'start' => $cursor->format('H:i'),
                'end'   => $cursor->copy()->addMinutes($duration)->format('H:i'),
            ];
            $cursor->addMinutes(30);
        }

        $existingAppointments = Appointment::where('professional_id', $professionalId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelado')
            ->get(['start_time', 'end_time']);

        $slots = collect($allSlots)->map(function ($slot) use ($existingAppointments) {
            $slotStart = Carbon::parse($slot['start']);
            $slotEnd   = Carbon::parse($slot['end']);
            $available = true;

            foreach ($existingAppointments as $appointment) {
                $apptStart = Carbon::parse($appointment->start_time);
                $apptEnd   = Carbon::parse($appointment->end_time);

                if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                    $available = false;
                    break;
                }
            }

            return [
                'start'     => $slot['start'],
                'end'       => $slot['end'],
                'available' => $available,
            ];
        })->values();

        return response()->json(['slots' => $slots]);
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
