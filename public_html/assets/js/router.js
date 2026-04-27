/**
 * Barbearia Turetta — Router
 * 
 * Client-side routing baseado em hash (#).
 * Funciona em qualquer hosting sem configuração de servidor.
 */

const Router = (() => {
    const routes = {};
    let notFoundHandler = null;
    let beforeEachGuard = null;

    /**
     * Registra uma rota
     * @param {string} path - Ex: '/', '/agendamento', '/admin/dashboard'
     * @param {Function} handler - Função que renderiza a view
     */
    function on(path, handler) {
        routes[path] = handler;
    }

    /**
     * Define handler para rota não encontrada
     */
    function onNotFound(handler) {
        notFoundHandler = handler;
    }

    /**
     * Define guard global executado antes de cada navegação
     * @param {Function} guard - Recebe (path) e retorna true/false ou novo path
     */
    function beforeEach(guard) {
        beforeEachGuard = guard;
    }

    /**
     * Navega para uma rota
     */
    function navigate(path) {
        window.location.hash = `#${path}`;
    }

    /**
     * Resolve a rota atual
     */
    function resolve() {
        let path = window.location.hash.slice(1) || '/';

        // Guard global
        if (beforeEachGuard) {
            const result = beforeEachGuard(path);
            if (result === false) return;
            if (typeof result === 'string') {
                navigate(result);
                return;
            }
        }

        // Match exato
        if (routes[path]) {
            routes[path]();
            return;
        }

        // Match com parâmetros dinâmicos (ex: /admin/clientes/:id)
        for (const [routePath, handler] of Object.entries(routes)) {
            const routeParts = routePath.split('/');
            const pathParts = path.split('/');

            if (routeParts.length !== pathParts.length) continue;

            const params = {};
            let match = true;

            for (let i = 0; i < routeParts.length; i++) {
                if (routeParts[i].startsWith(':')) {
                    params[routeParts[i].slice(1)] = pathParts[i];
                } else if (routeParts[i] !== pathParts[i]) {
                    match = false;
                    break;
                }
            }

            if (match) {
                handler(params);
                return;
            }
        }

        // 404
        if (notFoundHandler) {
            notFoundHandler(path);
        }
    }

    /**
     * Inicializa o router ouvindo mudanças de hash
     */
    function init() {
        window.addEventListener('hashchange', resolve);
        resolve();
    }

    return { on, onNotFound, beforeEach, navigate, init, resolve };
})();
