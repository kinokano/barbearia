<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Barbearia Turetta — Sistema de agendamento online.">
    <title>@yield('title', 'Turetta') — Barbearia Turetta</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            black: '#0a0a0a',
                            white: '#fafafa',
                            gray:  '#71717a',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        body { background-color: #0a0a0a; color: #fafafa; }

        /* Scrollbar minimalista */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }

        /* Transições suaves */
        .transition-smooth { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Glassmorphism card */
        .glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.06); }

        /* Hover lift */
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.4); }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col antialiased">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 border-b border-zinc-800/60 bg-zinc-950/80 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center group-hover:scale-105 transition-smooth">
                        <span class="text-black font-extrabold text-lg leading-none">T</span>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">TURETTA</span>
                </a>

                {{-- Navegação --}}
                <div class="flex items-center gap-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth {{ request()->routeIs('admin.dashboard') ? 'text-white' : '' }}">Agenda</a>
                            <a href="{{ route('admin.services.index') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth {{ request()->routeIs('admin.services.*') ? 'text-white' : '' }}">Serviços</a>
                            <a href="{{ route('admin.professionals.index') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth {{ request()->routeIs('admin.professionals.*') ? 'text-white' : '' }}">Profissionais</a>
                            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth {{ request()->routeIs('admin.schedules.*') ? 'text-white' : '' }}">Horários</a>
                            <a href="{{ route('admin.clients') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth {{ request()->routeIs('admin.clients') ? 'text-white' : '' }}">Clientes</a>
                        @elseif(auth()->user()->isProfessional())
                            <a href="{{ route('professional.agenda') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth">Minha Agenda</a>
                        @endif

                        <div class="h-5 w-px bg-zinc-700 mx-1"></div>

                        <span class="text-xs text-zinc-500">{{ auth()->user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-zinc-500 hover:text-red-400 transition-smooth">Sair</button>
                        </form>
                    @else
                        <a href="{{ route('client.booking') }}" class="text-sm text-zinc-400 hover:text-white transition-smooth">Agendar</a>
                        <a href="{{ route('login') }}" class="text-sm px-4 py-2 border border-zinc-700 rounded-lg hover:bg-white hover:text-black transition-smooth">Entrar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Conteúdo --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-zinc-800/60 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
            <span class="text-xs text-zinc-600">&copy; {{ date('Y') }} Barbearia Turetta. Todos os direitos reservados.</span>
            <span class="text-xs text-zinc-700">Sistema de Agendamentos</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
