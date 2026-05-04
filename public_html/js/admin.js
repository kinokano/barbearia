const API_BASE = '/api';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('pt-BR');
}

function formatTime(timeStr) {
    if (!timeStr) return '-';
    return timeStr.substring(0, 5);
}

function todayISO() {
    return new Date().toISOString().split('T')[0];
}

function switchTab(tab) {
    $$('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');

    $$('.tab-content').forEach(el => el.style.display = 'none');
    $(`#tab-${tab === 'agenda' ? 'agenda' : 'clients'}`).style.display = '';

    if (tab === 'clients') loadClients();
}

async function loadAppointments(date) {
    const tbody = $('#appointments-tbody');
    tbody.innerHTML = '<tr><td colspan="7"><div class="loading"><div class="spinner"></div>Carregando...</div></td></tr>';
    $('#agenda-empty').style.display = 'none';

    try {
        const res = await fetch(`${API_BASE}/admin/appointments?data=${date}`);
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = '';
            $('#agenda-empty').style.display = '';
            return;
        }

        tbody.innerHTML = data.map(a => `
            <tr>
                <td>${formatTime(a.horario_agendamento)}</td>
                <td>${a.user?.nome || '-'}</td>
                <td>${a.user?.telefone || '-'}</td>
                <td>${a.professional?.nome || '-'}</td>
                <td>${a.service?.nome || '-'}</td>
                <td><span class="badge badge--${a.status}">${a.status}</span></td>
                <td>
                    <select class="input" style="padding:0.4rem;font-size:0.8rem;width:auto;" onchange="updateStatus(${a.id}, this.value)">
                        <option value="agendado" ${a.status === 'agendado' ? 'selected' : ''}>Agendado</option>
                        <option value="confirmado" ${a.status === 'confirmado' ? 'selected' : ''}>Confirmado</option>
                        <option value="concluido" ${a.status === 'concluido' ? 'selected' : ''}>Concluído</option>
                        <option value="cancelado" ${a.status === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                    </select>
                </td>
            </tr>
        `).join('');
    } catch {
        tbody.innerHTML = '';
        showToast('Erro ao carregar agenda.', 'error');
    }
}

async function loadClients() {
    const tbody = $('#clients-tbody');
    tbody.innerHTML = '<tr><td colspan="4"><div class="loading"><div class="spinner"></div>Carregando...</div></td></tr>';
    $('#clients-empty').style.display = 'none';

    try {
        const res = await fetch(`${API_BASE}/admin/clients`);
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = '';
            $('#clients-empty').style.display = '';
            return;
        }

        tbody.innerHTML = data.map(c => `
            <tr>
                <td>${c.nome}</td>
                <td>${c.telefone}</td>
                <td>${formatDate(c.data_nascimento)}</td>
                <td>${c.appointments_count}</td>
            </tr>
        `).join('');
    } catch {
        tbody.innerHTML = '';
        showToast('Erro ao carregar clientes.', 'error');
    }
}

async function updateStatus(id, status) {
    try {
        const res = await fetch(`${API_BASE}/admin/appointments/${id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ status }),
        });

        if (!res.ok) throw new Error();
        showToast('Status atualizado.');
    } catch {
        showToast('Erro ao atualizar status.', 'error');
        loadAppointments($('#filter-date').value);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const filterDate = $('#filter-date');
    filterDate.value = todayISO();
    loadAppointments(todayISO());

    filterDate.addEventListener('change', () => {
        loadAppointments(filterDate.value);
    });

    $('#btn-filter-today').addEventListener('click', () => {
        filterDate.value = todayISO();
        loadAppointments(todayISO());
    });

    $$('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });
});
