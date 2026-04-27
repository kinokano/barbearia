/**
 * Barbearia Turetta — Datepicker Component
 * 
 * Calendário minimalista P&B para seleção de data.
 */

const Datepicker = (() => {
    const MONTH_NAMES = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    const DAY_NAMES = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    /**
     * Renderiza o datepicker em um container
     * @param {HTMLElement} container 
     * @param {Object} options
     * @param {Function} options.onSelect - Callback com a data selecionada (YYYY-MM-DD)
     * @param {string[]} options.disabledDates - Datas desabilitadas
     * @param {number[]} options.disabledDays - Dias da semana desabilitados (0=Dom)
     * @param {number} options.minDaysAhead - Mínimo de dias no futuro
     */
    function render(container, options = {}) {
        const {
            onSelect = () => {},
            disabledDates = [],
            disabledDays = [0], // Domingo off por padrão
            minDaysAhead = 0,
        } = options;

        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDate = null;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const minDate = new Date(today);
        minDate.setDate(minDate.getDate() + minDaysAhead);

        function buildCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            let daysHtml = '';

            // Dias em branco antes do primeiro dia
            for (let i = 0; i < firstDay; i++) {
                daysHtml += '<div class="datepicker__day datepicker__day--empty"></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentYear, currentMonth, day);
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                const isPast = date < minDate;
                const isDisabledDay = disabledDays.includes(date.getDay());
                const isDisabledDate = disabledDates.includes(dateStr);
                const isSelected = selectedDate === dateStr;
                const isToday = date.toDateString() === today.toDateString();

                let classes = 'datepicker__day';
                if (isPast || isDisabledDay || isDisabledDate) classes += ' datepicker__day--disabled';
                if (isSelected) classes += ' datepicker__day--selected';
                if (isToday) classes += ' datepicker__day--today';

                daysHtml += `<div class="${classes}" data-date="${dateStr}">${day}</div>`;
            }

            container.innerHTML = `
                <div class="datepicker">
                    <div class="datepicker__header">
                        <button class="datepicker__nav" id="dp-prev" aria-label="Mês anterior">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="10,2 4,8 10,14"/></svg>
                        </button>
                        <span class="datepicker__title">${MONTH_NAMES[currentMonth]} ${currentYear}</span>
                        <button class="datepicker__nav" id="dp-next" aria-label="Próximo mês">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6,2 12,8 6,14"/></svg>
                        </button>
                    </div>
                    <div class="datepicker__weekdays">
                        ${DAY_NAMES.map(d => `<div class="datepicker__weekday">${d}</div>`).join('')}
                    </div>
                    <div class="datepicker__days">${daysHtml}</div>
                </div>
            `;

            // Events
            container.querySelector('#dp-prev').addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) { currentMonth = 11; currentYear--; }
                buildCalendar();
            });

            container.querySelector('#dp-next').addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) { currentMonth = 0; currentYear++; }
                buildCalendar();
            });

            container.querySelectorAll('.datepicker__day:not(.datepicker__day--disabled):not(.datepicker__day--empty)')
                .forEach(el => {
                    el.addEventListener('click', () => {
                        selectedDate = el.dataset.date;
                        buildCalendar();
                        onSelect(selectedDate);
                    });
                });
        }

        buildCalendar();
    }

    return { render };
})();
