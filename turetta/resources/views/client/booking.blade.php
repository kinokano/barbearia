@extends('layouts.app')

@section('title', 'Agendar Horário')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Progress Steps --}}
    <div class="flex items-center justify-center gap-2 mb-10">
        @for($i = 1; $i <= 4; $i++)
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-smooth
                    {{ $step >= $i ? 'bg-white text-black' : 'bg-zinc-800 text-zinc-500' }}">
                    {{ $i }}
                </div>
                @if($i < 4)
                    <div class="w-8 sm:w-14 h-px {{ $step > $i ? 'bg-white' : 'bg-zinc-700' }}"></div>
                @endif
            </div>
        @endfor
    </div>

    {{-- Step 1: Serviço --}}
    @if($step === 1)
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight">Escolha o serviço</h1>
            <p class="text-zinc-500 text-sm mt-2">Selecione o serviço que deseja agendar</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($services as $service)
                <a href="{{ route('client.booking.professional', $service) }}" class="glass rounded-2xl p-6 hover-lift block group">
                    <h3 class="text-lg font-semibold text-white group-hover:text-zinc-100">{{ $service->name }}</h3>
                    @if($service->description)
                        <p class="text-zinc-500 text-sm mt-1">{{ $service->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 mt-4 text-sm">
                        <span class="text-zinc-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $service->duration_minutes }} min
                        </span>
                        <span class="text-white font-semibold">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Step 2: Profissional --}}
    @if($step === 2)
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight">Escolha o profissional</h1>
            <p class="text-zinc-500 text-sm mt-2">Quem você gostaria que realizasse o serviço <strong class="text-white">{{ $service->name }}</strong>?</p>
        </div>

        @if($professionals->isEmpty())
            <div class="glass rounded-2xl p-12 text-center">
                <p class="text-zinc-400 text-sm">Nenhum profissional disponível para este serviço.</p>
                <a href="{{ route('client.booking') }}" class="text-white text-sm underline mt-2 inline-block">Voltar</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($professionals as $prof)
                    <a href="{{ route('client.booking.slot', [$service, $prof]) }}" class="glass rounded-2xl p-6 hover-lift block group">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-zinc-400 uppercase group-hover:bg-white group-hover:text-black transition-smooth">
                                {{ mb_substr($prof->user->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">{{ $prof->user->name }}</h3>
                                <p class="text-zinc-500 text-sm">{{ $prof->specialty ?? 'Barbeiro' }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-6">
            <a href="{{ route('client.booking') }}" class="text-sm text-zinc-500 hover:text-white transition-smooth">← Voltar aos serviços</a>
        </div>
    @endif

    {{-- Step 3: Data e Horário --}}
    @if($step === 3)
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight">Escolha data e horário</h1>
            <p class="text-zinc-500 text-sm mt-2">{{ $service->name }} com <strong class="text-white">{{ $professional->user->name }}</strong></p>
        </div>

        <div class="glass rounded-2xl p-8">
            <div class="mb-6">
                <label for="booking-date" class="block text-sm font-medium text-zinc-300 mb-2">Data</label>
                <input type="date" id="booking-date" min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
            </div>

            <div id="slots-container" class="hidden">
                <p class="text-sm font-medium text-zinc-300 mb-3">Horários disponíveis</p>
                <div id="slots-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-2"></div>
                <p id="slots-empty" class="text-zinc-500 text-sm text-center py-6 hidden">Nenhum horário disponível para esta data.</p>
                <div id="slots-loading" class="text-center py-6 hidden">
                    <div class="inline-flex items-center gap-2 text-zinc-400 text-sm">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Carregando...
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('client.booking.professional', $service) }}" class="text-sm text-zinc-500 hover:text-white transition-smooth">← Voltar aos profissionais</a>
        </div>
    @endif

    {{-- Step 4: Confirmação --}}
    @if($step === 4)
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight">Confirme seu agendamento</h1>
            <p class="text-zinc-500 text-sm mt-2">Preencha seus dados para finalizar</p>
        </div>

        {{-- Resumo --}}
        <div class="glass rounded-2xl p-6 mb-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Serviço</p>
                    <p class="text-white font-medium">{{ $service->name }}</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Profissional</p>
                    <p class="text-white font-medium">{{ $professional->user->name }}</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Data</p>
                    <p class="text-white font-medium">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Horário</p>
                    <p class="text-white font-mono font-medium">{{ $start_time }} — {{ $end_time }}</p>
                </div>
            </div>
        </div>

        {{-- Formulário --}}
        <div class="glass rounded-2xl p-8">
            <form method="POST" action="{{ route('client.booking.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <input type="hidden" name="professional_id" value="{{ $professional->id }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="start_time" value="{{ $start_time }}">
                <input type="hidden" name="end_time" value="{{ $end_time }}">

                <div>
                    <label for="client_name" class="block text-sm font-medium text-zinc-300 mb-2">Nome completo</label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name', auth()->user()?->name ?? '') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm" placeholder="Seu nome completo">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="client_phone" class="block text-sm font-medium text-zinc-300 mb-2">WhatsApp / Telefone</label>
                        <input type="tel" id="client_phone" name="client_phone" value="{{ old('client_phone', auth()->user()?->phone ?? '') }}" required class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/20 text-sm" placeholder="(11) 99999-9999">
                    </div>
                    <div>
                        <label for="client_birth_date" class="block text-sm font-medium text-zinc-300 mb-2">Data de nascimento</label>
                        <input type="date" id="client_birth_date" name="client_birth_date" value="{{ old('client_birth_date', auth()->user()?->birth_date?->format('Y-m-d') ?? '') }}" class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 active:scale-[0.98] transition-smooth text-sm">
                    Confirmar Agendamento
                </button>
            </form>
        </div>
    @endif
</div>

@if($step === 3)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput     = document.getElementById('booking-date');
    const container     = document.getElementById('slots-container');
    const grid          = document.getElementById('slots-grid');
    const emptyMsg      = document.getElementById('slots-empty');
    const loadingMsg    = document.getElementById('slots-loading');
    const serviceId     = {{ $service->id }};
    const professionalId = {{ $professional->id }};

    dateInput.addEventListener('change', async function() {
        const date = this.value;
        if (!date) return;

        container.classList.remove('hidden');
        grid.innerHTML = '';
        emptyMsg.classList.add('hidden');
        loadingMsg.classList.remove('hidden');

        try {
            const res = await fetch(`{{ route('client.booking.slots') }}?date=${date}&professional_id=${professionalId}&service_id=${serviceId}`);
            const data = await res.json();
            loadingMsg.classList.add('hidden');

            if (data.slots.length === 0) {
                emptyMsg.textContent = data.message || 'Nenhum horário disponível para esta data.';
                emptyMsg.classList.remove('hidden');
                return;
            }

            data.slots.forEach(slot => {
                const btn = document.createElement('a');
                btn.href = `{{ route('client.booking.confirm') }}?service_id=${serviceId}&professional_id=${professionalId}&date=${date}&start_time=${slot.start}&end_time=${slot.end}`;
                btn.className = 'px-3 py-3 border border-zinc-700 rounded-xl text-center text-sm font-mono text-white hover:bg-white hover:text-black transition-smooth cursor-pointer';
                btn.textContent = slot.start;
                grid.appendChild(btn);
            });
        } catch (err) {
            loadingMsg.classList.add('hidden');
            emptyMsg.textContent = 'Erro ao carregar horários. Tente novamente.';
            emptyMsg.classList.remove('hidden');
        }
    });
});
</script>
@endpush
@endif
@endsection
