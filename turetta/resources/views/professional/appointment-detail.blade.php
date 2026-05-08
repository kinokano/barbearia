@extends('layouts.app')

@section('title', 'Detalhes do Agendamento')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('professional.agenda') }}" class="text-sm text-zinc-500 hover:text-white transition-smooth mb-6 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Voltar à agenda
    </a>

    <div class="glass rounded-2xl p-8 mt-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold tracking-tight">Detalhes</h1>
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $appointment->status_badge }}">{{ ucfirst($appointment->status) }}</span>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Serviço</p>
                    <p class="text-white font-medium">{{ $appointment->service->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Data</p>
                    <p class="text-white font-medium">{{ $appointment->date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Horário</p>
                    <p class="text-white font-mono">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Preço</p>
                    <p class="text-white font-medium">R$ {{ number_format($appointment->service->price, 2, ',', '.') }}</p>
                </div>
            </div>

            <hr class="border-zinc-800">

            <div>
                <p class="text-xs text-zinc-500 uppercase tracking-wider mb-3">Dados do Cliente</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-zinc-600 mb-0.5">Nome</p>
                        <p class="text-white">{{ $appointment->client_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-600 mb-0.5">Telefone</p>
                        <p class="text-white font-mono text-sm">{{ $appointment->client_phone }}</p>
                    </div>
                </div>

                @php $phone = preg_replace('/\D/', '', $appointment->client_phone); @endphp
                <a href="https://wa.me/55{{ $phone }}" target="_blank" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-400 rounded-lg text-sm hover:bg-emerald-500/20 transition-smooth">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Abrir WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
