const API_BASE = '/api';

const state = {
    currentStep: 1,
    totalSteps: 5,
    data_agendamento: null,
    horario_agendamento: null,
    professional_id: null,
    service_id: null,
};

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

function showToast(message, type = 'success') {
    const existing = $('.toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function setMinDate() {
    const input = $('#input-data');
    const today = new Date().toISOString().split('T')[0];
    input.min = today;
}

function goToStep(step) {
    state.currentStep = step;
    $$('.form-step').forEach(el => el.classList.remove('active'));
    $(`#step-${step}`).classList.add('active');

    $$('.step').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.remove('active', 'completed');
        if (s === step) el.classList.add('active');
        else if (s < step) el.classList.add('completed');
    });

    $('#btn-prev').style.display = step > 1 ? '' : 'none';
    $('#btn-next').style.display = step < state.totalSteps ? '' : 'none';
    $('#btn-submit').style.display = step === state.totalSteps ? '' : 'none';
}

async function fetchProfessionals() {
    try {
        const res = await fetch(`${API_BASE}/professionals`);
        const data = await res.json();
        const container = $('#professionals-container');
        container.innerHTML = '';
        data.forEach(p => {
            const card = document.createElement('div');
            card.className = 'option-card';
            card.dataset.id = p.id;
            card.innerHTML = `<div class="option-card__name">${p.nome}</div>`;
            card.addEventListener('click', () => {
                container.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                state.professional_id = p.id;
            });
            container.appendChild(card);
        });
    } catch {
        showToast('Erro ao carregar profissionais.', 'error');
    }
}

async function fetchServices() {
    try {
        const res = await fetch(`${API_BASE}/services`);
        const data = await res.json();
        const container = $('#services-container');
        container.innerHTML = '';
        data.forEach(s => {
            const card = document.createElement('div');
            card.className = 'option-card';
            card.dataset.id = s.id;
            card.innerHTML = `
                <div class="option-card__name">${s.nome}</div>
                <div class="option-card__detail">${s.descricao || ''}</div>
                <div class="option-card__price">R$ ${parseFloat(s.preco).toFixed(2).replace('.', ',')}</div>
            `;
            card.addEventListener('click', () => {
                container.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                state.service_id = s.id;
            });
            container.appendChild(card);
        });
    } catch {
        showToast('Erro ao carregar serviços.', 'error');
    }
}

async function fetchSlots() {
    if (!state.data_agendamento || !state.professional_id) return;
    const container = $('#slots-container');
    container.innerHTML = '<div class="loading"><div class="spinner"></div>Carregando...</div>';

    try {
        const res = await fetch(`${API_BASE}/slots?data=${state.data_agendamento}&professional_id=${state.professional_id}`);
        const data = await res.json();
        container.innerHTML = '';

        if (!data.slots || data.slots.length === 0) {
            container.innerHTML = '<div class="empty-state"><div class="empty-state__icon">📅</div><p>Nenhum horário disponível.</p></div>';
            return;
        }

        data.slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'slot-btn';
            btn.textContent = slot;
            btn.addEventListener('click', () => {
                container.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                state.horario_agendamento = slot;
            });
            container.appendChild(btn);
        });
    } catch {
        container.innerHTML = '<div class="empty-state"><p>Erro ao carregar horários.</p></div>';
    }
}

function validateStep(step) {
    switch (step) {
        case 1:
            state.data_agendamento = $('#input-data').value;
            if (!state.data_agendamento) { showToast('Selecione um dia.', 'error'); return false; }
            return true;
        case 2:
            if (!state.horario_agendamento) { showToast('Selecione um horário.', 'error'); return false; }
            return true;
        case 3:
            if (!state.professional_id) { showToast('Selecione um profissional.', 'error'); return false; }
            return true;
        case 4:
            if (!state.service_id) { showToast('Selecione um serviço.', 'error'); return false; }
            return true;
        default:
            return true;
    }
}

async function handleNext() {
    if (!validateStep(state.currentStep)) return;

    if (state.currentStep === 1) {
        await fetchProfessionals();
    }

    if (state.currentStep === 2) {
    }

    if (state.currentStep === 3) {
        await fetchSlots();
        goToStep(2);
        return;
    }

    goToStep(state.currentStep + 1);

    if (state.currentStep === 4) {
        await fetchServices();
    }
}

async function handleSubmit(e) {
    e.preventDefault();

    const nome = $('#input-nome').value.trim();
    const data_nascimento = $('#input-nascimento').value;
    const telefone = $('#input-whatsapp').value.trim();

    if (!nome || !data_nascimento || !telefone) {
        showToast('Preencha todos os campos.', 'error');
        return;
    }

    const btn = $('#btn-submit');
    btn.disabled = true;
    btn.textContent = 'Enviando...';

    try {
        const res = await fetch(`${API_BASE}/appointments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                nome,
                data_nascimento,
                telefone,
                professional_id: state.professional_id,
                service_id: state.service_id,
                data_agendamento: state.data_agendamento,
                horario_agendamento: state.horario_agendamento,
            }),
        });

        const data = await res.json();

        if (!res.ok) {
            showToast(data.error || 'Erro ao agendar.', 'error');
            btn.disabled = false;
            btn.textContent = 'Confirmar Agendamento';
            return;
        }

        $('#booking-form').style.display = 'none';
        $('#steps-indicator').style.display = 'none';
        $('#success-message').style.display = '';
        showToast('Agendamento confirmado!');
    } catch {
        showToast('Erro de conexão.', 'error');
        btn.disabled = false;
        btn.textContent = 'Confirmar Agendamento';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setMinDate();

    $('#btn-next').addEventListener('click', handleNext);

    $('#btn-prev').addEventListener('click', () => {
        if (state.currentStep > 1) {
            goToStep(state.currentStep - 1);
        }
    });

    $('#booking-form').addEventListener('submit', handleSubmit);

    $('#input-data').addEventListener('change', () => {
        state.data_agendamento = $('#input-data').value;
        state.horario_agendamento = null;
        state.professional_id = null;
    });
});
