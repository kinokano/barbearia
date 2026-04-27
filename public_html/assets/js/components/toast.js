/**
 * Barbearia Turetta — Toast Notifications
 */

const Toast = (() => {
    let container = null;

    function ensureContainer() {
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
    }

    /**
     * Exibe uma notificação toast
     * @param {string} message
     * @param {string} type - 'success' | 'error' | 'warning' | 'info'
     * @param {number} duration - ms
     */
    function show(message, type = 'info', duration = 4000) {
        ensureContainer();

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ',
        };

        const toast = document.createElement('div');
        toast.className = `toast toast--${type} slide-up`;
        toast.innerHTML = `
            <span class="toast__icon">${icons[type] || icons.info}</span>
            <span class="toast__message">${Utils.escapeHtml(message)}</span>
            <button class="toast__close" aria-label="Fechar">&times;</button>
        `;

        container.appendChild(toast);

        // Close button
        toast.querySelector('.toast__close').addEventListener('click', () => removeToast(toast));

        // Auto remove
        setTimeout(() => removeToast(toast), duration);
    }

    function removeToast(toast) {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }

    return { show };
})();
