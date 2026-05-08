@extends('layouts.app')

@section('title', 'Horários de Funcionamento')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Horários</h1>
            <p class="text-zinc-500 text-sm mt-1">Dias e horários de atendimento por profissional</p>
        </div>
        <a href="{{ route('admin.schedules.create') }}" class="px-4 py-2 bg-white text-black rounded-xl text-sm font-medium hover:bg-zinc-200 transition-smooth">+ Novo Horário</a>
    </div>

    @foreach($professionals as $prof)
        <div class="glass rounded-2xl p-6 mb-6">
            <h2 class="text-lg font-bold text-white mb-4">{{ $prof->user->name }}</h2>
            @if($prof->schedules->isEmpty())
                <p class="text-zinc-500 text-sm">Nenhum horário configurado.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($prof->schedules as $schedule)
                        <div class="flex items-center justify-between p-3 bg-zinc-900/50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-white">{{ $schedule->day_name }}</p>
                                <p class="text-xs text-zinc-500 font-mono">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="px-2 py-1 text-xs text-zinc-400 hover:text-white transition-smooth">Editar</a>
                                <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Excluir?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs text-red-400 hover:text-red-300 transition-smooth">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
