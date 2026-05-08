@extends('layouts.app')

@section('title', 'Agendamento Confirmado')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-8rem)] px-4">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold tracking-tight mb-3">Agendamento enviado!</h1>
        <p class="text-zinc-400 text-sm mb-2">Seu agendamento foi registrado com sucesso.</p>
        <p class="text-zinc-500 text-sm mb-8">O administrador da Barbearia Turetta entrará em contato via <strong class="text-emerald-400">WhatsApp</strong> para confirmar o horário.</p>

        <a href="{{ route('client.booking') }}" class="inline-flex px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-smooth text-sm">
            Fazer novo agendamento
        </a>
    </div>
</div>
@endsection
