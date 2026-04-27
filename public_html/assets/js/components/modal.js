/**
 * Barbearia Turetta — Modal Component
 */

const Modal = (() => {
    let currentModal = null;

    /**
     * Abre um modal
     * @param {Object} config
     * @param {string} config.title
     * @param {string} config.content - HTML interno
     * @param {string} config.confirmText
     * @param {string} config.cancelText
     * @param {string} config.variant - 'default' | 'danger'
     * @param {Function} config.onConfirm
     * @param {Function} config.onCancel
     */
    function open(config = {}) {
        close(); // Fecha modal anterior se existir

        const {
            title = '',
            content = '',
            confirmText = 'Confirmar',
            cancelText = 'Cancelar',
            variant = 'default',
            onConfirm = null,
            onCancel = null,
            showCancel = true,
        } = config;

        const btnClass = variant === 'danger' ? 'btn--danger' : 'btn--primary';

        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay fade-in';
        overlay.id = 'modal-overlay';
        overlay.innerHTML = `
            <div class="modal slide-up" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                ${title ? `
                    <div class="modal__header">
                        <h2 class="modal__title" id="modal-title">${title}</h2>
                        <button class="modal__close" id="modal-close" aria-label="Fechar">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="4" y1="4" x2="16" y2="16"/>
                                <line x1="16" y1="4" x2="4" y2="16"/>
                            </svg>
                        </button>
                    </div>
                ` : ''}
                <div class="modal__body">${content}</div>
                <div class="modal__footer">
                    ${showCancel ? `<button class="btn btn--ghost" id="modal-cancel">${cancelText}</button>` : ''}
                    <button class="btn ${btnClass}" id="modal-confirm">${confirmText}</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        currentModal = overlay;

        // Events
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                if (onCancel) onCancel();
                close();
            }
        });

        const closeBtn = document.getElementById('modal-close');
        if (closeBtn) closeBtn.addEventListener('click', () => { if (onCancel) onCancel(); close(); });

        const cancelBtn = document.getElementById('modal-cancel');
        if (cancelBtn) cancelBtn.addEventListener('click', () => { if (onCancel) onCancel(); close(); });

        const confirmBtn = document.getElementById('modal-confirm');
        if (confirmBtn) confirmBtn.addEventListener('click', () => { if (onConfirm) onConfirm(); close(); });

        // ESC para fechar
        document.addEventListener('keydown', handleEsc);
    }

    function handleEsc(e) {
        if (e.key === 'Escape') close();
    }

    function close() {
        if (currentModal) {
            currentModal.remove();
            currentModal = null;
            document.body.style.overflow = '';
            document.removeEventListener('keydown', handleEsc);
        }
    }

    return { open, close };
})();
