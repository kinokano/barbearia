@extends('layouts.app')

@section('title', 'Clientes — CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Clientes</h1>
            <p class="text-zinc-500 text-sm mt-1">CRM — Base de clientes da barbearia</p>
        </div>

        {{-- Busca --}}
        <form method="GET" action="{{ route('admin.clients') }}" class="flex items-center gap-2">
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Buscar por nome ou telefone..."
                class="px-4 py-2 bg-zinc-900 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 text-sm focus:outline-none focus:ring-2 focus:ring-white/20 transition-smooth w-64"
            >
            <button type="submit" class="px-4 py-2 bg-white text-black rounded-xl text-sm font-medium hover:bg-zinc-200 transition-smooth">
                Buscar
            </button>
        </form>
    </div>

    {{-- Tabela CRM --}}
    @if($clients->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <div class="w-12 h-12 bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-zinc-400 text-sm">Nenhum cliente encontrado.</p>
        </div>
    @else
        <div class="glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="clients-table">
                    <thead>
                        <tr class="border-b border-zinc-800">
                            <th class="text-left px-6 py-4 text-xs font-medium text-zinc-500 uppercase tracking-wider">Nome</th>
                            <th class="text-left px-6 py-4 text-xs font-medium text-zinc-500 uppercase tracking-wider">Telefone</th>
                            <th class="text-left px-6 py-4 text-xs font-medium text-zinc-500 uppercase tracking-wider">Aniversário</th>
                            <th class="text-left px-6 py-4 text-xs font-medium text-zinc-500 uppercase tracking-wider">Último Serviço</th>
                            <th class="text-left px-6 py-4 text-xs font-medium text-zinc-500 uppercase tracking-wider">Contato</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @foreach($clients as $client)
                            @php
                                $phone      = preg_replace('/\D/', '', $client->client_phone);
                                $lastDate   = $client->last_service_date ? \Carbon\Carbon::parse($client->last_service_date) : null;
                                $birthDate  = $client->client_birth_date ? \Carbon\Carbon::parse($client->client_birth_date) : null;

                                // Destaque para aniversariantes do mês
                                $isBirthdayMonth = $birthDate && $birthDate->month === now()->month;
                            @endphp
                            <tr class="hover:bg-zinc-800/30 transition-smooth">
                                {{-- Nome --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-zinc-800 rounded-full flex items-center justify-center text-xs font-bold text-zinc-400 uppercase flex-shrink-0">
                                            {{ mb_substr($client->client_name, 0, 2) }}
                                        </div>
                                        <span class="text-white font-medium">{{ $client->client_name }}</span>
                                    </div>
                                </td>

                                {{-- Telefone --}}
                                <td class="px-6 py-4 text-zinc-300 font-mono text-xs">
                                    {{ $client->client_phone }}
                                </td>

                                {{-- Aniversário --}}
                                <td class="px-6 py-4">
                                    @if($birthDate)
                                        <span class="inline-flex items-center gap-1.5 {{ $isBirthdayMonth ? 'text-yellow-400' : 'text-zinc-400' }}">
                                            @if($isBirthdayMonth)
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zm7-10a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0V6h-1a1 1 0 110-2h1V3a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                                            @endif
                                            {{ $birthDate->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Último Serviço --}}
                                <td class="px-6 py-4">
                                    @if($lastDate)
                                        <span class="text-zinc-300">{{ $lastDate->format('d/m/Y') }}</span>
                                        <span class="text-zinc-600 text-xs ml-1">({{ $lastDate->diffForHumans() }})</span>
                                    @else
                                        <span class="text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- WhatsApp --}}
                                <td class="px-6 py-4">
                                    <a
                                        href="https://wa.me/55{{ $phone }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs font-medium hover:bg-emerald-500/20 transition-smooth"
                                        title="Abrir conversa no WhatsApp"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $clients->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
