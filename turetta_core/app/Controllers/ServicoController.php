<?php
/**
 * ServicoController
 */
class ServicoController
{
    /**
     * Listar serviços ativos (público)
     */
    public function index(array $params, array $body): void
    {
        $db = Connection::getInstance();
        $stmt = $db->query('SELECT id, nome, descricao, duracao_minutos, preco, ativo FROM servicos WHERE ativo = 1 ORDER BY nome ASC');
        Response::success($stmt->fetchAll());
    }

    public function store(array $params, array $body): void
    {
        $data = Sanitizer::all($body, [
            'nome'            => 'string',
            'descricao'       => 'string',
            'duracao_minutos' => 'int',
            'preco'           => 'float',
        ]);

        require_once APP_PATH . '/Validators/ServicoValidator.php';
        $errors = ServicoValidator::validate($data);
        if (!empty($errors)) Response::validationError($errors);

        $db = Connection::getInstance();
        $stmt = $db->prepare('INSERT INTO servicos (nome, descricao, duracao_minutos, preco, ativo, created_at) VALUES (?, ?, ?, ?, 1, NOW())');
        $stmt->execute([$data['nome'], $data['descricao'], $data['duracao_minutos'], $data['preco']]);

        Response::created(['id' => $db->lastInsertId()], 'Serviço criado.');
    }

    public function update(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $data = Sanitizer::all($body, [
            'nome'            => 'string',
            'descricao'       => 'string',
            'duracao_minutos' => 'int',
            'preco'           => 'float',
            'ativo'           => 'int',
        ]);

        $db = Connection::getInstance();
        $stmt = $db->prepare('UPDATE servicos SET nome = ?, descricao = ?, duracao_minutos = ?, preco = ?, ativo = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$data['nome'], $data['descricao'], $data['duracao_minutos'], $data['preco'], $data['ativo'], $id]);

        if ($stmt->rowCount() === 0) Response::notFound('Serviço não encontrado.');

        Response::success(null, 'Serviço atualizado.');
    }

    public function destroy(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $db = Connection::getInstance();

        $stmt = $db->prepare('UPDATE servicos SET ativo = 0, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) Response::notFound('Serviço não encontrado.');

        Response::success(null, 'Serviço desativado.');
    }
}
