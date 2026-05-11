@extends('layouts.app')

@section('title', isset($professional) ? 'Editar Profissional' : 'Novo Profissional')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold tracking-tight mb-8">{{ isset($professional) ? 'Editar Profissional' : 'Novo Profissional' }}</h1>

    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ isset($professional) ? route('admin.professionals.update', $professional) : route('admin.professionals.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if(isset($professional)) @method('PUT') @endif

            <div>
                <label for="name" class="block text-sm font-medium text-zinc-300 mb-2">Nome completo</label>
                <input type="text" id="name" name="name" value="{{ old('name', $professional->user->name ?? '') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $professional->user->email ?? '') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-zinc-300 mb-2">Telefone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $professional->user->phone ?? '') }}" class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">Senha{{ isset($professional) ? ' (deixe vazio para manter)' : '' }}</label>
                    <input type="password" id="password" name="password" {{ !isset($professional) ? 'required' : '' }} class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
                <div>
                    <label for="specialty" class="block text-sm font-medium text-zinc-300 mb-2">Especialidade</label>
                    <input type="text" id="specialty" name="specialty" value="{{ old('specialty', $professional->specialty ?? '') }}" placeholder="Ex: Corte e Barba" class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                </div>
            </div>

            <div>
                <label for="profile_photo" class="block text-sm font-medium text-zinc-300 mb-2">Foto de Perfil (Opcional)</label>
                @if(isset($professional) && $professional->profile_photo)
                    <div class="mb-3">
                        <img src="{{ Storage::url($professional->profile_photo) }}" alt="Foto" class="w-16 h-16 rounded-full object-cover border border-zinc-700">
                    </div>
                @endif
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="w-full px-4 py-2 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-white file:text-black hover:file:bg-zinc-200 transition-smooth text-sm focus:outline-none focus:ring-2 focus:ring-white/20">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Serviços que realiza</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($services as $svc)
                        <label class="flex items-center gap-2 p-3 bg-zinc-900/50 rounded-lg cursor-pointer hover:bg-zinc-800/50 transition-smooth">
                            <input type="checkbox" name="services[]" value="{{ $svc->id }}"
                                {{ in_array($svc->id, old('services', isset($professional) ? $professional->services->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                class="w-4 h-4 rounded bg-zinc-800 border-zinc-600 accent-white">
                            <span class="text-sm text-zinc-300">{{ $svc->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            @if(isset($professional))
                <div class="flex items-center gap-2">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" id="active" name="active" value="1" {{ old('active', $professional->active) ? 'checked' : '' }} class="w-4 h-4 rounded bg-zinc-800 border-zinc-600 accent-white">
                    <label for="active" class="text-sm text-zinc-400">Ativo</label>
                </div>
            @endif

            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-smooth text-sm">Salvar</button>
                <a href="{{ route('admin.professionals.index') }}" class="px-6 py-3 border border-zinc-700 rounded-xl text-sm text-zinc-400 hover:text-white transition-smooth">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
