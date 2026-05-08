@extends('layouts.app')

@section('title', 'Agenda Geral')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Agenda Geral</h1>
            <p class="text-zinc-500 text-sm mt-1">
                {{ \Carbon\Carbon::parse($date)->locale('pt_BR')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </p>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="px-4 py-2 bg-zinc-900 border border-zinc-700/50 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-white/20 transition-smooth">
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="glass rounded-xl p-5">
            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Total do dia</p>
            <p class="text-3xl font-bold">{{ $appointments->count() }}</p>
        </div>
        <div class="glass rounded-xl p-5">
            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Pendentes</p>
            <p class="text-3xl font-bold text-yellow-400">{{ $appointments->where('status', 'pendente')->count() }}</p>
        </div>
        <div class="glass rounded-xl p-5">
            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Agendados</p>
            <p class="text-3xl font-bold text-emerald-400">{{ $appointments->where('status', 'agendado')->count() }}</p>
        </div>
        <div class="glass rounded-xl p-5">
            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Cancelados</p>
            <p class="text-3xl font-bold text-red-400">{{ $appointments->where('status', 'cancelado')->count() }}</p>
        </div>
    </div>

    {{-- Lista de Agendamentos --}}
    @if($appointments->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <div class="w-12 h-12 bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-sm">Nenhum agendamento para esta data.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($appointments as $appt)
                <div class="glass rounded-xl overflow-hidden" id="appt-{{ $appt->id }}">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5">

                        {{-- Horário --}}
                        <div class="flex-shrink-0 text-center sm:w-24">
                            <p class="text-xl font-bold font-mono text-white">{{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }}</p>
                            <p class="text-xs text-zinc-500 font-mono">{{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}</p>
                        </div>

                        <div class="hidden sm:block w-px h-10 bg-zinc-700 flex-shrink-0"></div>

                        {{-- Cliente + Serviço --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <p class="text-white font-semibold">{{ $appt->client_name }}</p>
                                <span class="text-zinc-600 text-xs">·</span>
                                <p class="text-zinc-400 text-sm">{{ $appt->service->name }}</p>
                                <span class="text-zinc-600 text-xs">·</span>
                                <p class="text-zinc-500 text-sm">{{ $appt->professional->user->name }}</p>
                            </div>
                            <p class="text-zinc-500 text-sm font-mono">{{ $appt->client_phone }}</p>
                        </div>

                        {{-- Status badge --}}
                        <div class="flex-shrink-0">
                            @php
                                $statusClasses = match($appt->status) {
                                    'pendente'  => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                    'agendado'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'cancelado' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    default     => 'bg-zinc-800 text-zinc-400 border-zinc-700',
                                };
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- WhatsApp --}}
                            @php $phone = preg_replace('/\D/', '', $appt->client_phone); @endphp
                            <a href="https://wa.me/55{{ $phone }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-smooth" title="WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>

                            {{-- Botão alterar status --}}
                            <button
                                onclick="toggleStatusPanel({{ $appt->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-zinc-700 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition-smooth"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Status
                            </button>
                        </div>
                    </div>

                    {{-- Painel de alteração de status (escondido por padrão) --}}
                    <div id="status-panel-{{ $appt->id }}" class="hidden border-t border-zinc-800 bg-zinc-900/50 px-5 py-4">
                        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-3">Alterar status do agendamento</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['pendente' => ['label' => 'Pendente', 'class' => 'border-yellow-700 text-yellow-400 hover:bg-yellow-500/10'], 'agendado' => ['label' => 'Agendado', 'class' => 'border-emerald-700 text-emerald-400 hover:bg-emerald-500/10'], 'cancelado' => ['label' => 'Cancelado', 'class' => 'border-red-800 text-red-400 hover:bg-red-500/10']] as $statusValue => $config)
                                <form method="POST" action="{{ route('admin.appointments.status', $appt) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $statusValue }}">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-lg border text-sm font-medium transition-smooth {{ $config['class'] }} {{ $appt->status === $statusValue ? 'ring-2 ring-offset-1 ring-offset-zinc-900 ring-current opacity-60 cursor-default' : '' }}"
                                        {{ $appt->status === $statusValue ? 'disabled' : '' }}
                                    >
                                        {{ $config['label'] }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleStatusPanel(id) {
    const panel = document.getElementById('status-panel-' + id);
    panel.classList.toggle('hidden');
}
</script>
@endpush
@endsection
