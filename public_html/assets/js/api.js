/**
 * Barbearia Turetta — API Client
 * 
 * Wrapper sobre fetch() com:
 * - Base URL automática
 * - JWT injection no header Authorization
 * - Interceptor de 401 (token expirado → refresh)
 * - Respostas padronizadas { success, data, message }
 */

const Api = (() => {
    const BASE_URL = (() => {
        const loc = window.location;
        return `${loc.protocol}//${loc.host}/api`;
    })();

    /**
     * Request genérico
     */
    async function request(method, endpoint, body = null, options = {}) {
        const url = `${BASE_URL}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers,
        };

        // Injeta token JWT se existir
        const token = Auth.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            method,
            headers,
        };

        if (body && method !== 'GET') {
            config.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, config);

            // Token expirado → tenta refresh
            if (response.status === 401 && !options._isRetry) {
                const refreshed = await Auth.refreshToken();
                if (refreshed) {
                    return request(method, endpoint, body, { ...options, _isRetry: true });
                } else {
                    Auth.logout();
                    Router.navigate('/');
                    throw new Error('Sessão expirada. Faça login novamente.');
                }
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `Erro ${response.status}`);
            }

            return data;
        } catch (error) {
            if (error instanceof TypeError) {
                throw new Error('Sem conexão com o servidor. Tente novamente.');
            }
            throw error;
        }
    }

    return {
        get:    (endpoint, options)       => request('GET', endpoint, null, options),
        post:   (endpoint, body, options) => request('POST', endpoint, body, options),
        put:    (endpoint, body, options) => request('PUT', endpoint, body, options),
        delete: (endpoint, options)       => request('DELETE', endpoint, null, options),
    };
})();
