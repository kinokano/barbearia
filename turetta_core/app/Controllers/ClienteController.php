<?php
/**
 * ClienteController
 */
class ClienteController
{
    public function index(array $params, array $body): void
    {
        $db = Connection::getInstance();
        $busca = $_GET['busca'] ?? '';

        if (!empty($busca)) {
            $stmt = $db->prepare('
                SELECT u.id, u.nome, u.email, u.telefone, u.created_at,
                       COUNT(a.id) as total_agendamentos
                FROM usuarios u
                LEFT JOIN agendamentos a ON a.cliente_id = u.id
                WHERE u.role = "cliente" AND (u.nome LIKE ? OR u.email LIKE ? OR u.telefone LIKE ?)
                GROUP BY u.id
                ORDER BY u.nome ASC
            ');
            $like = "%$busca%";
            $stmt->execute([$like, $like, $like]);
        } else {
            $stmt = $db->query('
                SELECT u.id, u.nome, u.email, u.telefone, u.created_at,
                       COUNT(a.id) as total_agendamentos
                FROM usuarios u
                LEFT JOIN agendamentos a ON a.cliente_id = u.id
                WHERE u.role = "cliente"
                GROUP BY u.id
                ORDER BY u.nome ASC
            ');
        }

        Response::success($stmt->fetchAll());
    }

    public function show(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $db = Connection::getInstance();

        $stmt = $db->prepare('SELECT id, nome, email, telefone, created_at FROM usuarios WHERE id = ? AND role = "cliente"');
        $stmt->execute([$id]);
        $cliente = $stmt->fetch();

        if (!$cliente) Response::notFound('Cliente não encontrado.');

        // Últimos agendamentos
        $stmt = $db->prepare('
            SELECT a.*, s.nome as servico_nome, p.nome as profissional_nome
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            JOIN profissionais p ON a.profissional_id = p.id
            WHERE a.cliente_id = ?
            ORDER BY a.data DESC, a.hora DESC
            LIMIT 20
        ');
        $stmt->execute([$id]);
        $cliente['agendamentos'] = $stmt->fetchAll();

        Response::success($cliente);
    }

    public function store(array $params, array $body): void
    {
        $data = Sanitizer::all($body, [
            'nome'     => 'string',
            'email'    => 'email',
            'telefone' => 'digits',
        ]);

        require_once APP_PATH . '/Validators/ClienteValidator.php';
        $errors = ClienteValidator::validate($data);
        if (!empty($errors)) Response::validationError($errors);

        $db = Connection::getInstance();

        // Check email duplicado
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) Response::error('E-mail já cadastrado.', 409);

        // Senha padrão (cliente poderá redefinir)
        $senha = password_hash('turetta123', PASSWORD_BCRYPT);

        $stmt = $db->prepare('INSERT INTO usuarios (nome, email, telefone, senha, role, ativo, created_at) VALUES (?, ?, ?, ?, "cliente", 1, NOW())');
        $stmt->execute([$data['nome'], $data['email'], $data['telefone'], $senha]);

        $userId = $db->lastInsertId();
        $stmt = $db->prepare('INSERT INTO clientes (usuario_id, created_at) VALUES (?, NOW())');
        $stmt->execute([$userId]);

        Response::created(['id' => $userId, 'nome' => $data['nome']], 'Cliente cadastrado.');
    }

    public function update(array $params, array $body): void
    {
        $id = Sanitizer::int($params['id'] ?? 0);
        $data = Sanitizer::all($body, [
            'nome'     => 'string',
            'email'    => 'email',
            'telefone' => 'digits',
        ]);

        $db = Connection::getInstance();
        $stmt = $db->prepare('UPDATE usuarios SET nome = ?, email = ?, telefone = ?, updated_at = NOW() WHERE id = ? AND role = "cliente"');
        $stmt->execute([$data['nome'], $data['email'], $data['telefone'], $id]);

        if ($stmt->rowCount() === 0) Response::notFound('Cliente não encontrado.');

        Response::success(null, 'Cliente atualizado.');
    }
}
