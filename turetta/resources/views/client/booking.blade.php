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
                            @if($prof->profile_photo)
                                <img src="{{ Storage::url($prof->profile_photo) }}" alt="{{ $prof->user->name }}" class="w-14 h-14 rounded-full object-cover border border-zinc-700 transition-smooth group-hover:border-white">
                            @else
                                <div class="w-14 h-14 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-zinc-400 uppercase group-hover:bg-white group-hover:text-black transition-smooth">
                                    {{ mb_substr($prof->user->name, 0, 2) }}
                                </div>
                            @endif
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
        <div x-data="{
            selectedDate: null,
            slots: [],
            loading: false,
            noSlots: false,
            errorMsg: '',
            serviceId: {{ $service->id }},
            professionalId: {{ $professional->id }},
            async loadSlots(date) {
                this.selectedDate = date;
                this.slots = [];
                this.loading = true;
                this.noSlots = false;
                this.errorMsg = '';
                try {
                    const res = await fetch(`{{ route('client.booking.slots') }}?date=${date}&professional_id=${this.professionalId}&service_id=${this.serviceId}`);
                    const data = await res.json();
                    this.loading = false;
                    if (data.slots.length === 0) {
                        this.noSlots = true;
                        this.errorMsg = data.message || 'Nenhum horário disponível para esta data.';
                    } else {
                        this.slots = data.slots;
                    }
                } catch (e) {
                    this.loading = false;
                    this.noSlots = true;
                    this.errorMsg = 'Erro ao carregar horários. Tente novamente.';
                }
            },
            slotUrl(slot) {
                return `{{ route('client.booking.confirm') }}?service_id=${this.serviceId}&professional_id=${this.professionalId}&date=${this.selectedDate}&start_time=${slot.start}&end_time=${slot.end}`;
            }
        }">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-white">Escolha o horário</h1>
                <p class="text-zinc-500 text-sm mt-1">Selecione uma data e horário disponível</p>
            </div>

            {{-- Seletor de Semana --}}
            <div class="flex items-center justify-between mb-5">
                @if($canGoPrev)
                    <a href="{{ route('client.booking.slot', [$service, $professional, 'week' => $weekOffset - 1]) }}"
                       class="w-10 h-10 flex items-center justify-center rounded-lg border border-zinc-700/60 text-zinc-400 hover:text-white hover:border-zinc-500 transition-smooth">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @else
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg border border-zinc-800/40 text-zinc-700 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </div>
                @endif

                <span class="text-sm font-semibold text-white tracking-wide">{{ $weekLabel }}</span>

                <a href="{{ route('client.booking.slot', [$service, $professional, 'week' => $weekOffset + 1]) }}"
                   class="w-10 h-10 flex items-center justify-center rounded-lg border border-zinc-700/60 text-zinc-400 hover:text-white hover:border-zinc-500 transition-smooth">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Cards de Dias --}}
            <div class="grid grid-cols-7 gap-2 mb-8">
                @foreach($days as $d)
                    @if($d['available'])
                        <button @click="loadSlots('{{ $d['date'] }}')"
                                :class="selectedDate === '{{ $d['date'] }}'
                                    ? 'border-yellow-600/60 bg-zinc-800/80 ring-1 ring-yellow-700/30'
                                    : 'border-zinc-700/50 bg-zinc-900/60 hover:border-zinc-500'"
                                class="relative flex flex-col items-center justify-center py-3 rounded-xl border transition-smooth cursor-pointer">
                            <span class="text-[11px] font-medium tracking-wider"
                                  :class="selectedDate === '{{ $d['date'] }}' ? 'text-yellow-200/80' : 'text-zinc-400'">{{ $d['label'] }}</span>
                            <span class="text-lg font-bold mt-0.5"
                                  :class="selectedDate === '{{ $d['date'] }}' ? 'text-white' : 'text-zinc-200'">{{ $d['day'] }}</span>
                            <span x-show="selectedDate === '{{ $d['date'] }}'" class="absolute bottom-1.5 w-1 h-1 rounded-full bg-yellow-500/80"></span>
                        </button>
                    @else
                        <div class="flex flex-col items-center justify-center py-3 rounded-xl border border-zinc-800/30 bg-zinc-900/30 opacity-40 cursor-not-allowed select-none">
                            <span class="text-[11px] font-medium tracking-wider text-zinc-600">{{ $d['label'] }}</span>
                            <span class="text-lg font-bold mt-0.5 text-zinc-600">{{ $d['day'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Horários --}}
            <div x-show="selectedDate" x-cloak>
                <p class="text-xs font-semibold text-zinc-500 tracking-widest uppercase mb-4">Horários Disponíveis</p>

                <div x-show="loading" class="flex items-center justify-center py-8">
                    <svg class="animate-spin w-5 h-5 text-zinc-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>

                <div x-show="noSlots && !loading" class="text-center py-8">
                    <p class="text-zinc-500 text-sm" x-text="errorMsg"></p>
                </div>

                <div x-show="slots.length > 0 && !loading" class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-2.5">
                    <template x-for="slot in slots" :key="slot.start">
                        <template x-if="slot.available">
                            <a :href="slotUrl(slot)"
                               class="px-3 py-3.5 bg-zinc-800/70 border border-zinc-700/40 rounded-xl text-center text-sm font-mono font-semibold text-white hover:bg-zinc-700 hover:border-zinc-500 active:scale-95 transition-smooth cursor-pointer"
                               x-text="slot.start"></a>
                        </template>
                        <template x-if="!slot.available">
                            <div class="px-3 py-3.5 bg-zinc-900/40 border border-zinc-800/30 rounded-xl text-center text-sm font-mono text-zinc-600 line-through opacity-50 cursor-not-allowed select-none"
                                 x-text="slot.start"></div>
                        </template>
                    </template>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('client.booking.professional', $service) }}" class="text-sm text-zinc-500 hover:text-white transition-smooth">← Voltar aos profissionais</a>
            </div>
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
                        <input type="text" id="client_birth_date" name="client_birth_date" value="{{ old('client_birth_date', auth()->user()?->birth_date?->format('Y-m-d') ?? '') }}" placeholder="Selecione uma data..." class="w-full px-4 py-3 bg-zinc-900/80 border border-zinc-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-white/20 text-sm cursor-pointer">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 active:scale-[0.98] transition-smooth text-sm">
                    Confirmar Agendamento
                </button>
            </form>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('client_birth_date')) {
        flatpickr("#client_birth_date", {
            locale: "pt",
            dateFormat: "Y-m-d",
            maxDate: "today",
            disableMobile: "true"
        });
    }
});
</script>
@endpush
@endsection
