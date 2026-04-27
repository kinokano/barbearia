/**
 * Barbearia Turetta — App Bootstrap
 * 
 * Inicializa o router, define as views e arranca a aplicação.
 */

(function () {
    'use strict';

    const main = document.getElementById('main-content');
    const navbarContainer = document.getElementById('navbar-container');

    // ── Render Navbar ────────────────────────────
    function renderNavbar() {
        Navbar.render(navbarContainer);
    }

    // ── Views ────────────────────────────────────

    /**
     * HOME — Landing Page
     */
    function viewHome() {
        main.innerHTML = `
            <!-- Hero -->
            <section class="hero">
                <div class="hero__content">
                    <span class="hero__tag">Barbearia Premium</span>
                    <h1 class="hero__title">Estilo que<br>define você.</h1>
                    <p class="hero__subtitle">Cortes modernos, ambiente exclusivo e atendimento de excelência. Agende seu horário em segundos.</p>
                    <div class="hero__actions">
                        <a href="#/agendamento" class="btn btn--primary btn--lg">Agendar Agora</a>
                        <a href="#servicos" class="btn btn--secondary btn--lg">Nossos Serviços</a>
                    </div>
                </div>
                <div class="hero__scroll">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>
                </div>
            </section>

            <!-- Stats -->
            <section class="section" style="border-bottom: 1px solid var(--border-color);">
                <div class="container">
                    <div class="stats">
                        <div>
                            <div class="stats__number">5.000+</div>
                            <div class="stats__label">Clientes atendidos</div>
                        </div>
                        <div>
                            <div class="stats__number">8</div>
                            <div class="stats__label">Anos de experiência</div>
                        </div>
                        <div>
                            <div class="stats__number">4.9★</div>
                            <div class="stats__label">Avaliação Google</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Serviços -->
            <section class="section" id="servicos">
                <div class="container">
                    <div class="section__header">
                        <span class="section__tag">Serviços</span>
                        <h2 class="section__title">O que oferecemos</h2>
                        <p class="section__subtitle">Cada detalhe pensado para a sua melhor experiência.</p>
                    </div>
                    <div class="grid grid--3">
                        <div class="card card--hoverable service-card">
                            <div class="service-card__icon">✂</div>
                            <h3 class="service-card__title">Corte Masculino</h3>
                            <p class="card__body">Corte moderno com acabamento na máquina e navalha. Inclui lavagem.</p>
                            <p class="service-card__price">R$ 45 <span>/ 40min</span></p>
                        </div>
                        <div class="card card--hoverable service-card">
                            <div class="service-card__icon">🪒</div>
                            <h3 class="service-card__title">Barba</h3>
                            <p class="card__body">Modelagem com navalha, toalha quente e produtos premium.</p>
                            <p class="service-card__price">R$ 35 <span>/ 30min</span></p>
                        </div>
                        <div class="card card--hoverable service-card">
                            <div class="service-card__icon">💈</div>
                            <h3 class="service-card__title">Combo Completo</h3>
                            <p class="card__body">Corte + Barba com desconto. A experiência completa Turetta.</p>
                            <p class="service-card__price">R$ 70 <span>/ 60min</span></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="cta">
                <div class="container text-center">
                    <span class="section__tag" style="color: var(--color-gray-500); border-color: var(--color-gray-700); border: 1px solid; padding: var(--space-2) var(--space-4); border-radius: var(--border-radius-full);">Pronto?</span>
                    <h2 class="section__title" style="color: var(--color-white); margin-top: var(--space-6);">Agende em 30 segundos.</h2>
                    <p class="section__subtitle" style="color: var(--color-gray-400);">Escolha o serviço, profissional, data e horário. Sem complicação.</p>
                    <a href="#/agendamento" class="btn btn--primary btn--lg mt-8">Agendar Horário</a>
                </div>
            </section>
        `;
    }

    /**
     * LOGIN
     */
    function viewLogin() {
        main.innerHTML = `
            <section class="section min-h-screen flex items-center justify-center" style="padding-top: 100px;">
                <div class="container container--sm">
                    <div class="card" style="padding: var(--space-10);">
                        <div class="text-center mb-8">
                            <h1 style="font-family: var(--font-display); font-size: var(--text-2xl); font-weight: var(--font-black);">Entrar</h1>
                            <p class="text-secondary mt-2" style="font-size: var(--text-sm);">Acesse sua conta para gerenciar seus agendamentos.</p>
                        </div>
                        <form id="login-form">
                            <div class="form-group">
                                <label class="form-label" for="login-email">E-mail</label>
                                <input class="form-input" type="email" id="login-email" placeholder="seu@email.com" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="login-senha">Senha</label>
                                <input class="form-input" type="password" id="login-senha" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn--primary btn--full btn--lg mt-4">Entrar</button>
                        </form>
                        <div class="divider"></div>
                        <p class="text-center text-sm text-secondary">
                            Não tem conta? <a href="#/register" style="color: var(--text-primary); font-weight: var(--font-semibold);">Cadastre-se</a>
                        </p>
                    </div>
                </div>
            </section>
        `;

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const senha = document.getElementById('login-senha').value;

            try {
                const res = await Auth.login(email, senha);
                if (res.success) {
                    Toast.show('Bem-vindo de volta!', 'success');
                    renderNavbar();
                    Router.navigate(Auth.isAdmin() ? '/admin/dashboard' : '/cliente');
                }
            } catch (err) {
                Toast.show(err.message, 'error');
            }
        });
    }

    /**
     * REGISTER
     */
    function viewRegister() {
        main.innerHTML = `
            <section class="section min-h-screen flex items-center justify-center" style="padding-top: 100px;">
                <div class="container container--sm">
                    <div class="card" style="padding: var(--space-10);">
                        <div class="text-center mb-8">
                            <h1 style="font-family: var(--font-display); font-size: var(--text-2xl); font-weight: var(--font-black);">Criar Conta</h1>
                            <p class="text-secondary mt-2" style="font-size: var(--text-sm);">Cadastre-se para agendar e gerenciar seus horários.</p>
                        </div>
                        <form id="register-form">
                            <div class="form-group">
                                <label class="form-label" for="reg-nome">Nome completo</label>
                                <input class="form-input" type="text" id="reg-nome" placeholder="João Silva" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="reg-email">E-mail</label>
                                <input class="form-input" type="email" id="reg-email" placeholder="seu@email.com" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="reg-telefone">Telefone</label>
                                <input class="form-input" type="tel" id="reg-telefone" placeholder="(11) 99999-9999" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="reg-senha">Senha</label>
                                <input class="form-input" type="password" id="reg-senha" placeholder="Mínimo 6 caracteres" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn--primary btn--full btn--lg mt-4">Criar Conta</button>
                        </form>
                        <div class="divider"></div>
                        <p class="text-center text-sm text-secondary">
                            Já tem conta? <a href="#/login" style="color: var(--text-primary); font-weight: var(--font-semibold);">Entrar</a>
                        </p>
                    </div>
                </div>
            </section>
        `;

        document.getElementById('reg-telefone').addEventListener('input', (e) => Utils.maskPhone(e.target));

        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const dados = {
                nome: document.getElementById('reg-nome').value,
                email: document.getElementById('reg-email').value,
                telefone: document.getElementById('reg-telefone').value,
                senha: document.getElementById('reg-senha').value,
            };

            if (!Utils.isValidEmail(dados.email)) return Toast.show('E-mail inválido.', 'error');
            if (!Utils.isValidPhone(dados.telefone)) return Toast.show('Telefone inválido.', 'error');

            try {
                const res = await Auth.register(dados);
                if (res.success) {
                    Toast.show('Conta criada com sucesso!', 'success');
                    renderNavbar();
                    Router.navigate('/agendamento');
                }
            } catch (err) {
                Toast.show(err.message, 'error');
            }
        });
    }

    /**
     * AGENDAMENTO — Fluxo público
     */
    function viewAgendamento() {
        main.innerHTML = `
            <section class="section" style="padding-top: 100px; min-height: 100vh;">
                <div class="container container--md">
                    <div class="section__header" style="text-align: left;">
                        <span class="section__tag">Agendamento</span>
                        <h1 class="section__title">Escolha seu horário</h1>
                        <p class="section__subtitle" style="margin-left: 0;">Selecione o serviço, profissional, data e horário disponível.</p>
                    </div>

                    <!-- Step Indicators -->
                    <div class="flex gap-3 mb-8" id="step-indicators">
                        <div class="badge badge--neutral" data-step="1">1. Serviço</div>
                        <div class="badge badge--neutral" data-step="2">2. Profissional</div>
                        <div class="badge badge--neutral" data-step="3">3. Data & Hora</div>
                        <div class="badge badge--neutral" data-step="4">4. Confirmação</div>
                    </div>

                    <div id="booking-steps">
                        <!-- Steps rendered dynamically -->
                    </div>
                </div>
            </section>
        `;

        // Agendamento flow seria controlado pelo agendamento.js
        const stepsContainer = document.getElementById('booking-steps');
        stepsContainer.innerHTML = `
            <div class="grid grid--3">
                <div class="card card--hoverable cursor-pointer" data-servico="1">
                    <div class="service-card__icon" style="margin: 0 0 var(--space-4) 0;">✂</div>
                    <h3 class="card__title">Corte Masculino</h3>
                    <p class="card__subtitle">40 min — R$ 45,00</p>
                </div>
                <div class="card card--hoverable cursor-pointer" data-servico="2">
                    <div class="service-card__icon" style="margin: 0 0 var(--space-4) 0;">🪒</div>
                    <h3 class="card__title">Barba</h3>
                    <p class="card__subtitle">30 min — R$ 35,00</p>
                </div>
                <div class="card card--hoverable cursor-pointer" data-servico="3">
                    <div class="service-card__icon" style="margin: 0 0 var(--space-4) 0;">💈</div>
                    <h3 class="card__title">Combo Completo</h3>
                    <p class="card__subtitle">60 min — R$ 70,00</p>
                </div>
            </div>
        `;

        // Highlight first step
        document.querySelector('[data-step="1"]').classList.remove('badge--neutral');
        document.querySelector('[data-step="1"]').classList.add('badge--success');
    }

    /**
     * CLIENTE — Área restrita
     */
    function viewCliente() {
        main.innerHTML = `
            <section class="section" style="padding-top: 100px; min-height: 100vh;">
                <div class="container container--md">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h1 style="font-family: var(--font-display); font-size: var(--text-2xl); font-weight: var(--font-black);">
                                ${Utils.getGreeting()}, ${Auth.getUser()?.nome?.split(' ')[0] || 'Cliente'}
                            </h1>
                            <p class="text-secondary text-sm mt-2">Gerencie seus próximos agendamentos.</p>
                        </div>
                        <a href="#/agendamento" class="btn btn--primary">Novo Agendamento</a>
                    </div>

                    <div class="card">
                        <div class="card__header">
                            <h2 class="card__title">Próximos Horários</h2>
                        </div>
                        <div id="agendamentos-list">
                            <div class="empty-state">
                                <div class="empty-state__icon">📋</div>
                                <p class="empty-state__title">Nenhum agendamento</p>
                                <p class="empty-state__text">Você ainda não tem horários marcados. Que tal agendar agora?</p>
                                <a href="#/agendamento" class="btn btn--primary">Agendar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `;
    }

    /**
     * 404 — Not Found
     */
    function view404(path) {
        main.innerHTML = `
            <section class="section min-h-screen flex items-center justify-center">
                <div class="text-center">
                    <h1 style="font-family: var(--font-display); font-size: var(--text-5xl); font-weight: var(--font-black);">404</h1>
                    <p class="text-secondary text-lg mt-4">Página não encontrada.</p>
                    <a href="#/" class="btn btn--primary mt-8">Voltar ao Início</a>
                </div>
            </section>
        `;
    }

    // ── Route Registration ───────────────────────
    Router.on('/', viewHome);
    Router.on('/login', viewLogin);
    Router.on('/register', viewRegister);
    Router.on('/agendamento', viewAgendamento);
    Router.on('/cliente', () => {
        if (!Auth.isAuthenticated()) {
            Toast.show('Faça login para continuar.', 'warning');
            return Router.navigate('/login');
        }
        viewCliente();
    });

    Router.onNotFound(view404);

    // ── Re-render navbar on every route change ──
    window.addEventListener('hashchange', () => {
        renderNavbar();
        window.scrollTo(0, 0);
    });

    // ── Init ─────────────────────────────────────
    renderNavbar();
    Router.init();

})();
