@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Serviços</h1>
            <p class="text-zinc-500 text-sm mt-1">Gerencie os serviços oferecidos</p>
        </div>
        <a href="{{ route('admin.services.create') }}" class="px-4 py-2 bg-white text-black rounded-xl text-sm font-medium hover:bg-zinc-200 transition-smooth">+ Novo Serviço</a>
    </div>

    @if($services->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <p class="text-zinc-400 text-sm">Nenhum serviço cadastrado.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $service)
                <div class="glass rounded-2xl p-6 hover-lift">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-lg font-semibold text-white">{{ $service->name }}</h3>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $service->active ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                            {{ $service->active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    @if($service->description)
                        <p class="text-zinc-500 text-sm mb-4">{{ $service->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-sm text-zinc-400 mb-5">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $service->duration_minutes }} min
                        </span>
                        <span class="font-mono font-medium text-white">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.services.edit', $service) }}" class="flex-1 text-center px-3 py-2 border border-zinc-700 rounded-lg text-xs text-zinc-300 hover:bg-zinc-800 transition-smooth">Editar</a>
                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Excluir este serviço?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-2 border border-red-800/30 rounded-lg text-xs text-red-400 hover:bg-red-500/10 transition-smooth">Excluir</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
