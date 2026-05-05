const API_BASE = '/api';

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
}

async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const btn = document.getElementById('btn-login');
    const errorEl = document.getElementById('login-error');
    
    btn.disabled = true;
    btn.textContent = 'Autenticando...';
    errorEl.style.display = 'none';

    try {
        // Primeiro, obtemos o cookie CSRF do Sanctum
        await fetch(`${API_BASE}/../sanctum/csrf-cookie`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const token = getCookie('XSRF-TOKEN');

        // Enviamos o formulário de login
        const response = await fetch(`${API_BASE}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': token
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                email,
                password
            })
        });

        if (response.ok) {
            // Login com sucesso, redireciona para o admin
            window.location.href = 'admin.html';
        } else {
            errorEl.style.display = 'block';
            errorEl.textContent = 'Credenciais inválidas.';
            btn.disabled = false;
            btn.textContent = 'Entrar no Painel';
        }
    } catch (error) {
        console.error('Erro no login:', error);
        errorEl.style.display = 'block';
        errorEl.textContent = 'Erro de conexão com o servidor.';
        btn.disabled = false;
        btn.textContent = 'Entrar no Painel';
    }
}
