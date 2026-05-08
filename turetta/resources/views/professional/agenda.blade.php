@extends('layouts.app')

@section('title', 'Minha Agenda')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Minha Agenda</h1>
            <p class="text-zinc-500 text-sm mt-1">Seus agendamentos do dia</p>
        </div>
        <form method="GET" action="{{ route('professional.agenda') }}">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="px-4 py-2 bg-zinc-900 border border-zinc-700/50 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-white/20 transition-smooth">
        </form>
    </div>

    @if($appointments->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <p class="text-zinc-400 text-sm">Nenhum agendamento para esta data.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($appointments as $appt)
                <a href="{{ route('professional.appointment.show', $appt) }}" class="block glass rounded-xl p-5 hover-lift">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <p class="text-xl font-bold font-mono text-white">{{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}</p>
                                <p class="text-xs text-zinc-500 font-mono">{{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}</p>
                            </div>
                            <div class="h-10 w-px bg-zinc-700"></div>
                            <div>
                                <p class="text-white font-medium">{{ $appt->client_name }}</p>
                                <p class="text-zinc-500 text-sm">{{ $appt->service->name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $appt->status_badge }}">{{ ucfirst($appt->status) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
