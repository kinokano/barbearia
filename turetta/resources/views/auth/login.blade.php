@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-8rem)] px-4">
    <div class="w-full max-w-md">
        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-white/5">
                <span class="text-black font-extrabold text-3xl leading-none">T</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Entrar no sistema</h1>
            <p class="text-zinc-500 text-sm mt-2">Acesse o painel da Barbearia Turetta</p>
        </div>

        {{-- Card de Login --}}
        <div class="glass rounded-2xl p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="seu@email.com"
                        class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-zinc-500 transition-smooth text-sm"
                    >
                </div>

                {{-- Senha --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">Senha</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-zinc-500 transition-smooth text-sm"
                    >
                </div>

                {{-- Lembrar --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded bg-zinc-800 border-zinc-600 text-white focus:ring-white/20 accent-white">
                    <label for="remember" class="text-sm text-zinc-400">Lembrar de mim</label>
                </div>

                {{-- Botão --}}
                <button
                    type="submit"
                    class="w-full py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 active:scale-[0.98] transition-smooth text-sm"
                >
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center text-zinc-600 text-xs mt-6">
            Quer agendar um horário? <a href="{{ route('client.booking') }}" class="text-white hover:underline">Clique aqui</a>
        </p>
    </div>
</div>
@endsection
