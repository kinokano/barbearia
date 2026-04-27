/**
 * Barbearia Turetta — Auth Module
 * 
 * Gerenciamento de autenticação via JWT.
 * - Login / Logout
 * - Token storage (localStorage)
 * - Refresh automático
 * - Guards de rota
 */

const Auth = (() => {
    const TOKEN_KEY = 'turetta_token';
    const REFRESH_KEY = 'turetta_refresh';
    const USER_KEY = 'turetta_user';

    /**
     * Retorna o token JWT atual
     */
    function getToken() {
        return localStorage.getItem(TOKEN_KEY);
    }

    /**
     * Retorna o usuário logado
     */
    function getUser() {
        const raw = localStorage.getItem(USER_KEY);
        return raw ? JSON.parse(raw) : null;
    }

    /**
     * Verifica se está autenticado
     */
    function isAuthenticated() {
        const token = getToken();
        if (!token) return false;

        // Verifica expiração do token
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.exp * 1000 > Date.now();
        } catch {
            return false;
        }
    }

    /**
     * Verifica se o usuário é admin
     */
    function isAdmin() {
        const user = getUser();
        return user && user.role === 'admin';
    }

    /**
     * Login
     */
    async function login(email, senha) {
        const response = await Api.post('/auth/login', { email, senha });

        if (response.success) {
            localStorage.setItem(TOKEN_KEY, response.data.token);
            localStorage.setItem(REFRESH_KEY, response.data.refresh_token);
            localStorage.setItem(USER_KEY, JSON.stringify(response.data.usuario));
        }

        return response;
    }

    /**
     * Registro de cliente
     */
    async function register(dados) {
        const response = await Api.post('/auth/register', dados);

        if (response.success) {
            localStorage.setItem(TOKEN_KEY, response.data.token);
            localStorage.setItem(REFRESH_KEY, response.data.refresh_token);
            localStorage.setItem(USER_KEY, JSON.stringify(response.data.usuario));
        }

        return response;
    }

    /**
     * Refresh do token
     */
    async function refreshToken() {
        const refresh = localStorage.getItem(REFRESH_KEY);
        if (!refresh) return false;

        try {
            const response = await Api.post('/auth/refresh', { refresh_token: refresh }, { _isRetry: true });
            if (response.success) {
                localStorage.setItem(TOKEN_KEY, response.data.token);
                localStorage.setItem(REFRESH_KEY, response.data.refresh_token);
                return true;
            }
        } catch {
            return false;
        }

        return false;
    }

    /**
     * Logout
     */
    function logout() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(REFRESH_KEY);
        localStorage.removeItem(USER_KEY);
    }

    /**
     * Guard para rotas que requerem autenticação
     */
    function requireAuth(path) {
        if (!isAuthenticated()) {
            Toast.show('Faça login para continuar.', 'warning');
            return '/';
        }
        return true;
    }

    /**
     * Guard para rotas admin
     */
    function requireAdmin(path) {
        if (!isAuthenticated() || !isAdmin()) {
            Toast.show('Acesso restrito.', 'error');
            return '/';
        }
        return true;
    }

    return {
        getToken,
        getUser,
        isAuthenticated,
        isAdmin,
        login,
        register,
        refreshToken,
        logout,
        requireAuth,
        requireAdmin,
    };
})();
