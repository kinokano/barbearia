/**
 * Barbearia Turetta — Timeslot Grid Component
 * 
 * Exibe grade de horários disponíveis para agendamento.
 */

const TimeslotGrid = (() => {

    /**
     * Renderiza a grade de horários
     * @param {HTMLElement} container
     * @param {Object} options
     * @param {Array} options.slots - Array de { time: '09:00', available: true }
     * @param {Function} options.onSelect - Callback com o horário selecionado
     */
    function render(container, options = {}) {
        const { slots = [], onSelect = () => {} } = options;

        if (slots.length === 0) {
            container.innerHTML = `
                <div class="empty-state" style="padding: var(--space-8);">
                    <div class="empty-state__icon">📅</div>
                    <p class="empty-state__title">Nenhum horário disponível</p>
                    <p class="empty-state__text">Selecione outra data ou profissional.</p>
                </div>
            `;
            return;
        }

        let selectedTime = null;

        function build() {
            container.innerHTML = `
                <div class="timeslot-grid">
                    ${slots.map(slot => {
                        let classes = 'timeslot';
                        if (!slot.available) classes += ' timeslot--disabled';
                        if (slot.time === selectedTime) classes += ' timeslot--selected';
                        return `<button class="${classes}" data-time="${slot.time}" ${!slot.available ? 'disabled' : ''}>${Utils.formatTime(slot.time)}</button>`;
                    }).join('')}
                </div>
            `;

            container.querySelectorAll('.timeslot:not(.timeslot--disabled)').forEach(el => {
                el.addEventListener('click', () => {
                    selectedTime = el.dataset.time;
                    build();
                    onSelect(selectedTime);
                });
            });
        }

        build();
    }

    return { render };
})();
