/**
 * Barbearia Turetta — Utility Functions
 */

const Utils = (() => {

    /**
     * Formata data para exibição: "18 de Abril de 2026"
     */
    function formatDate(dateStr) {
        const date = new Date(dateStr + 'T00:00:00');
        return date.toLocaleDateString('pt-BR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    }

    /**
     * Formata data curta: "18/04/2026"
     */
    function formatDateShort(dateStr) {
        const date = new Date(dateStr + 'T00:00:00');
        return date.toLocaleDateString('pt-BR');
    }

    /**
     * Formata hora: "14:30"
     */
    function formatTime(timeStr) {
        return timeStr.substring(0, 5);
    }

    /**
     * Formata preço: "R$ 45,00"
     */
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    }

    /**
     * Máscara de telefone: (11) 99999-9999
     */
    function maskPhone(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 11) value = value.substring(0, 11);

        if (value.length > 6) {
            value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
        } else if (value.length > 2) {
            value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
        } else if (value.length > 0) {
            value = `(${value}`;
        }

        input.value = value;
    }

    /**
     * Valida e-mail
     */
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * Valida telefone (10 ou 11 dígitos)
     */
    function isValidPhone(phone) {
        const digits = phone.replace(/\D/g, '');
        return digits.length >= 10 && digits.length <= 11;
    }

    /**
     * Debounce
     */
    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    /**
     * Gera iniciais de um nome: "João Silva" → "JS"
     */
    function getInitials(name) {
        return name
            .split(' ')
            .filter(Boolean)
            .map(w => w[0].toUpperCase())
            .slice(0, 2)
            .join('');
    }

    /**
     * Retorna saudação baseada na hora do dia
     */
    function getGreeting() {
        const hour = new Date().getHours();
        if (hour < 12) return 'Bom dia';
        if (hour < 18) return 'Boa tarde';
        return 'Boa noite';
    }

    /**
     * Gera ID único simples
     */
    function uid() {
        return Date.now().toString(36) + Math.random().toString(36).substring(2);
    }

    /**
     * Escapa HTML para prevenção de XSS
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /**
     * Retorna nome do dia da semana
     */
    function getDayName(dayIndex) {
        const days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        return days[dayIndex];
    }

    /**
     * Verifica se uma data é hoje
     */
    function isToday(dateStr) {
        const today = new Date().toISOString().split('T')[0];
        return dateStr === today;
    }

    /**
     * Verifica se uma data está no passado
     */
    function isPast(dateStr) {
        const today = new Date().toISOString().split('T')[0];
        return dateStr < today;
    }

    return {
        formatDate,
        formatDateShort,
        formatTime,
        formatCurrency,
        maskPhone,
        isValidEmail,
        isValidPhone,
        debounce,
        getInitials,
        getGreeting,
        uid,
        escapeHtml,
        getDayName,
        isToday,
        isPast,
    };
})();
