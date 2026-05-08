@extends('layouts.app')

@section('title', isset($schedule) ? 'Editar Horário' : 'Novo Horário')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold tracking-tight mb-8">{{ isset($schedule) ? 'Editar Horário' : 'Novo Horário' }}</h1>

    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ isset($schedule) ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}" class="space-y-5">
            @csrf
            @if(isset($schedule)) @method('PUT') @endif

            <div>
                <label for="professional_id" class="block text-sm font-medium text-zinc-300 mb-2">Profissional</label>
                <select id="professional_id" name="professional_id" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                    <option value="">Selecione...</option>
                    @foreach($professionals as $prof)
                        <option value="{{ $prof->id }}" {{ old('professional_id', $schedule->professional_id ?? '') == $prof->id ? 'selected' : '' }}>{{ $prof->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="day_of_week" class="block text-sm font-medium text-zinc-300 mb-2">Dia da semana</label>
                <select id="day_of_week" name="day_of_week" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                    @foreach(\App\Models\Schedule::DAYS as $num => $name)
                        <option value="{{ $num }}" {{ old('day_of_week', $schedule->day_of_week ?? '') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-zinc-300 mb-2">Início</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', isset($schedule) ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-zinc-300 mb-2">Fim</label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', isset($schedule) ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '18:00') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-smooth text-sm">Salvar</button>
                <a href="{{ route('admin.schedules.index') }}" class="px-6 py-3 border border-zinc-700 rounded-xl text-sm text-zinc-400 hover:text-white transition-smooth">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
