<?php
/**
 * ProfissionalController
 */
class ProfissionalController
{
    /**
     * Listar profissionais ativos (público)
     */
    public function index(array $params, array $body): void
    {
        $db = Connection::getInstance();
        $stmt = $db->query('SELECT id, nome, especialidades, foto, ativo FROM profissionais WHERE ativo = 1 ORDER BY nome ASC');
        Response::success($stmt->fetchAll());
    }

    public function store(array $params, array $body): void
    {
        $data = Sanitizer::all($body, [
            'nome'            => 'string',
            'especialidades'  => 'string',
        ]);

        if (empty($data['nome'])) {
            Response::validationError(['nome' => 'Nome é obrigatório.']);
        }

        $db = Connection::getInstance();
        $stmt = $db->prepare('INSERT INTO profissionais (nome, especialidades, ativo, created_at) VALUES (?, ?, 1, NOW())');
        $stmt->execute([$data['nome'], $data['especialidades']]);

        Response::created(['id' => $db->lastInsertId()], 'Profissional cadastrado.');
    }

    public function update(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $data = Sanitizer::all($body, [
            'nome'            => 'string',
            'especialidades'  => 'string',
            'ativo'           => 'int',
        ]);

        $db = Connection::getInstance();
        $stmt = $db->prepare('UPDATE profissionais SET nome = ?, especialidades = ?, ativo = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$data['nome'], $data['especialidades'], $data['ativo'], $id]);

        if ($stmt->rowCount() === 0) Response::notFound('Profissional não encontrado.');

        Response::success(null, 'Profissional atualizado.');
    }

    public function destroy(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $db = Connection::getInstance();

        $stmt = $db->prepare('UPDATE profissionais SET ativo = 0, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) Response::notFound('Profissional não encontrado.');

        Response::success(null, 'Profissional desativado.');
    }
}
