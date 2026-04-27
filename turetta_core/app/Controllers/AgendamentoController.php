<?php
/**
 * AgendamentoController
 */
class AgendamentoController
{
    /**
     * Verificar disponibilidade de horários (público)
     */
    public function disponibilidade(array $params, array $body): void
    {
        $profissionalId = Sanitizer::int($params['profissional_id'] ?? 0);
        $data = Sanitizer::string($params['data'] ?? '');

        if (!$profissionalId || !DateHelper::isValid($data)) {
            Response::validationError(['data' => 'Profissional e data são obrigatórios.']);
        }

        if (!DateHelper::isFuture($data) && $data !== DateHelper::today()) {
            Response::validationError(['data' => 'A data deve ser hoje ou no futuro.']);
        }

        require_once APP_PATH . '/Services/DisponibilidadeService.php';
        $slots = DisponibilidadeService::getAvailableSlots($profissionalId, $data);

        Response::success($slots);
    }

    /**
     * Criar agendamento (requer auth)
     */
    public function store(array $params, array $body): void
    {
        $user = AuthMiddleware::getUser();

        $data = Sanitizer::all($body, [
            'profissional_id' => 'int',
            'servico_id'      => 'int',
            'data'            => 'string',
            'hora'            => 'string',
        ]);

        // Validação
        require_once APP_PATH . '/Validators/AgendamentoValidator.php';
        $errors = AgendamentoValidator::validate($data);
        if (!empty($errors)) Response::validationError($errors);

        // Verificar conflito
        require_once APP_PATH . '/Services/AgendamentoService.php';
        $conflito = AgendamentoService::checkConflict($data['profissional_id'], $data['data'], $data['hora'], $data['servico_id']);
        if ($conflito) {
            Response::error('Horário indisponível. Por favor, escolha outro.', 409);
        }

        // Criar
        $agendamento = AgendamentoService::create([
            'cliente_id'      => $user['id'],
            'profissional_id' => $data['profissional_id'],
            'servico_id'      => $data['servico_id'],
            'data'            => $data['data'],
            'hora'            => $data['hora'],
            'status'          => 'confirmado',
        ]);

        Response::created($agendamento, 'Agendamento realizado com sucesso!');
    }

    /**
     * Listar agendamentos (admin)
     */
    public function adminIndex(array $params, array $body): void
    {
        $db = Connection::getInstance();

        $dataFiltro = $_GET['data'] ?? DateHelper::today();

        $stmt = $db->prepare('
            SELECT a.*, u.nome as cliente_nome, p.nome as profissional_nome, s.nome as servico_nome, s.preco
            FROM agendamentos a
            JOIN usuarios u ON a.cliente_id = u.id
            JOIN profissionais p ON a.profissional_id = p.id
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.data = ?
            ORDER BY a.hora ASC
        ');
        $stmt->execute([$dataFiltro]);

        Response::success($stmt->fetchAll());
    }

    /**
     * Atualizar status do agendamento (admin)
     */
    public function update(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $status = Sanitizer::string($body['status'] ?? '');

        $validStatuses = ['confirmado', 'concluido', 'cancelado', 'nao_compareceu'];
        if (!in_array($status, $validStatuses)) {
            Response::validationError(['status' => 'Status inválido.']);
        }

        $db = Connection::getInstance();
        $stmt = $db->prepare('UPDATE agendamentos SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);

        if ($stmt->rowCount() === 0) {
            Response::notFound('Agendamento não encontrado.');
        }

        Response::success(null, 'Agendamento atualizado.');
    }

    /**
     * Cancelar agendamento (admin)
     */
    public function destroy(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);

        $db = Connection::getInstance();
        $stmt = $db->prepare('UPDATE agendamentos SET status = "cancelado", updated_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            Response::notFound('Agendamento não encontrado.');
        }

        Response::success(null, 'Agendamento cancelado.');
    }
}
