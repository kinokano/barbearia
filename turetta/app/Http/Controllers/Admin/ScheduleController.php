<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $professionals = Professional::with(['user', 'schedules' => fn ($q) => $q->orderBy('day_of_week')])
            ->active()
            ->get();

        return view('admin.schedules.index', compact('professionals'));
    }

    public function create(): View
    {
        $professionals = Professional::with('user')->active()->get();

        return view('admin.schedules.form', compact('professionals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'exists:professionals,id'],
            'day_of_week'     => ['required', 'integer', 'between:0,6'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Verifica duplicidade
        $exists = Schedule::where('professional_id', $validated['professional_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['day_of_week' => 'Já existe um horário cadastrado para este profissional neste dia.']);
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Horário cadastrado com sucesso.');
    }

    public function edit(Schedule $schedule): View
    {
        $professionals = Professional::with('user')->active()->get();

        return view('admin.schedules.form', compact('schedule', 'professionals'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'exists:professionals,id'],
            'day_of_week'     => ['required', 'integer', 'between:0,6'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $exists = Schedule::where('professional_id', $validated['professional_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['day_of_week' => 'Já existe um horário cadastrado para este profissional neste dia.']);
        }

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Horário atualizado com sucesso.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Horário excluído com sucesso.');
    }
}
