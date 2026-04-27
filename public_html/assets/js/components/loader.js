/**
 * Barbearia Turetta — Loader Component
 */

const Loader = (() => {

    /**
     * Exibe skeleton loader em um container
     */
    function show(container, type = 'card', count = 3) {
        let html = '';

        switch (type) {
            case 'card':
                for (let i = 0; i < count; i++) {
                    html += `
                        <div class="card" style="padding: var(--space-6);">
                            <div class="skeleton skeleton--title"></div>
                            <div class="skeleton skeleton--text" style="width: 90%;"></div>
                            <div class="skeleton skeleton--text" style="width: 70%;"></div>
                            <div class="skeleton skeleton--text" style="width: 80%; margin-top: var(--space-4);"></div>
                        </div>
                    `;
                }
                break;

            case 'table':
                html = `
                    <div class="card">
                        ${Array(count).fill('').map(() => `
                            <div class="flex items-center gap-4" style="padding: var(--space-4) 0; border-bottom: 1px solid var(--border-color);">
                                <div class="skeleton skeleton--avatar"></div>
                                <div class="flex-1">
                                    <div class="skeleton skeleton--text" style="width: 40%;"></div>
                                    <div class="skeleton skeleton--text" style="width: 60%;"></div>
                                </div>
                                <div class="skeleton" style="width: 80px; height: 24px;"></div>
                            </div>
                        `).join('')}
                    </div>
                `;
                break;

            case 'inline':
                html = `
                    <div class="flex items-center justify-center gap-3" style="padding: var(--space-8);">
                        <div class="spinner"></div>
                        <span class="text-sm text-secondary">Carregando...</span>
                    </div>
                `;
                break;
        }

        container.innerHTML = html;
    }

    return { show };
})();
