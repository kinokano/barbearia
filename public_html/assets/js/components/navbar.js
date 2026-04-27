/**
 * Barbearia Turetta — Navbar Component
 */

const Navbar = (() => {
    function render(container) {
        const user = Auth.getUser();
        const isAuth = Auth.isAuthenticated();
        const isAdmin = Auth.isAdmin();

        container.innerHTML = `
            <header class="header" id="main-header">
                <div class="container header__inner">
                    <a href="#/" class="header__logo">TURETTA</a>

                    <button class="header__menu-btn" id="menu-toggle" aria-label="Menu">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>

                    <nav class="header__nav" id="main-nav">
                        <a href="#/" class="header__link" data-route="/">Início</a>
                        <a href="#/agendamento" class="header__link" data-route="/agendamento">Agendar</a>
                        ${isAuth && !isAdmin ? `
                            <a href="#/cliente" class="header__link" data-route="/cliente">Meus Horários</a>
                        ` : ''}
                        ${isAdmin ? `
                            <a href="#/admin/dashboard" class="header__link" data-route="/admin">Painel</a>
                        ` : ''}
                        ${isAuth ? `
                            <button class="btn btn--ghost btn--sm" id="btn-logout">Sair</button>
                        ` : `
                            <a href="#/login" class="btn btn--primary btn--sm">Entrar</a>
                        `}
                    </nav>
                </div>
            </header>
        `;

        // Menu mobile toggle
        const toggle = document.getElementById('menu-toggle');
        const nav = document.getElementById('main-nav');
        if (toggle) {
            toggle.addEventListener('click', () => nav.classList.toggle('open'));
        }

        // Logout
        const logoutBtn = document.getElementById('btn-logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                Auth.logout();
                Router.navigate('/');
                Toast.show('Até logo!', 'success');
            });
        }

        // Active link
        updateActiveLink();
    }

    function updateActiveLink() {
        const current = window.location.hash.slice(1) || '/';
        document.querySelectorAll('.header__link').forEach(link => {
            const route = link.dataset.route;
            if (route && current.startsWith(route)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    return { render, updateActiveLink };
})();
