@extends('layouts.app')

@section('title', isset($service) ? 'Editar Serviço' : 'Novo Serviço')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold tracking-tight mb-8">{{ isset($service) ? 'Editar Serviço' : 'Novo Serviço' }}</h1>

    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" class="space-y-5">
            @csrf
            @if(isset($service)) @method('PUT') @endif

            <div>
                <label for="name" class="block text-sm font-medium text-zinc-300 mb-2">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name', $service->name ?? '') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-zinc-300 mb-2">Descrição</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">{{ old('description', $service->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-zinc-300 mb-2">Duração (min)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? 30) }}" min="5" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-zinc-300 mb-2">Preço (R$)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $service->price ?? '') }}" min="0" step="0.01" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" id="active" name="active" value="1" {{ old('active', $service->active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-zinc-800 border-zinc-600 accent-white">
                <label for="active" class="text-sm text-zinc-400">Ativo</label>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-smooth text-sm">Salvar</button>
                <a href="{{ route('admin.services.index') }}" class="px-6 py-3 border border-zinc-700 rounded-xl text-sm text-zinc-400 hover:text-white transition-smooth">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
