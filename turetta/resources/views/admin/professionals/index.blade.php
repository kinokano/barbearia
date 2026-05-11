@extends('layouts.app')

@section('title', 'Profissionais')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Profissionais</h1>
            <p class="text-zinc-500 text-sm mt-1">Gerencie a equipe da barbearia</p>
        </div>
        <a href="{{ route('admin.professionals.create') }}" class="px-4 py-2 bg-white text-black rounded-xl text-sm font-medium hover:bg-zinc-200 transition-smooth">+ Novo Profissional</a>
    </div>

    @if($professionals->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <p class="text-zinc-400 text-sm">Nenhum profissional cadastrado.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($professionals as $prof)
                <div class="glass rounded-2xl p-6 hover-lift">
                    <div class="flex items-center gap-3 mb-4">
                        @if($prof->profile_photo)
                            <img src="{{ Storage::url($prof->profile_photo) }}" alt="{{ $prof->user->name }}" class="w-12 h-12 rounded-full object-cover border border-zinc-700">
                        @else
                            <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center text-sm font-bold text-zinc-400 uppercase">{{ mb_substr($prof->user->name, 0, 2) }}</div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $prof->user->name }}</h3>
                            <p class="text-zinc-500 text-xs">{{ $prof->specialty ?? 'Barbeiro' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($prof->services as $svc)
                            <span class="px-2 py-0.5 bg-zinc-800 rounded-full text-xs text-zinc-400">{{ $svc->name }}</span>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.professionals.edit', $prof) }}" class="flex-1 text-center px-3 py-2 border border-zinc-700 rounded-lg text-xs text-zinc-300 hover:bg-zinc-800 transition-smooth">Editar</a>
                        <form method="POST" action="{{ route('admin.professionals.destroy', $prof) }}" onsubmit="return confirm('Excluir?')">
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
