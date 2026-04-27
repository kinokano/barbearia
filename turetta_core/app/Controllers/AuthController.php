<?php
/**
 * AuthController — Autenticação
 */
class AuthController
{
    public function login(array $params, array $body): void
    {
        $email = Sanitizer::email($body['email'] ?? '');
        $senha = $body['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            Response::validationError(['email' => 'E-mail e senha são obrigatórios.']);
        }

        $db = Connection::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($senha, $user['senha'])) {
            Response::error('E-mail ou senha incorretos.', 401);
        }

        $token = AuthService::generateToken($user);
        $refreshToken = AuthService::generateRefreshToken($user);

        unset($user['senha']);

        Response::success([
            'token'         => $token,
            'refresh_token' => $refreshToken,
            'usuario'       => $user,
        ], 'Login realizado com sucesso.');
    }

    public function register(array $params, array $body): void
    {
        $data = Sanitizer::all($body, [
            'nome'     => 'string',
            'email'    => 'email',
            'telefone' => 'digits',
            'senha'    => 'string',
        ]);

        // Validação
        $errors = [];
        if (empty($data['nome']))  $errors['nome'] = 'Nome é obrigatório.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail inválido.';
        }
        if (strlen($data['telefone']) < 10) $errors['telefone'] = 'Telefone inválido.';
        if (strlen($body['senha'] ?? '') < 6)  $errors['senha'] = 'Senha deve ter no mínimo 6 caracteres.';

        if (!empty($errors)) Response::validationError($errors);

        $db = Connection::getInstance();

        // Verificar email duplicado
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            Response::error('Este e-mail já está cadastrado.', 409);
        }

        // Criar usuário
        $senhaHash = password_hash($body['senha'], PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO usuarios (nome, email, telefone, senha, role, ativo, created_at) VALUES (?, ?, ?, ?, "cliente", 1, NOW())');
        $stmt->execute([$data['nome'], $data['email'], $data['telefone'], $senhaHash]);

        $userId = $db->lastInsertId();

        // Criar registro na tabela clientes
        $stmt = $db->prepare('INSERT INTO clientes (usuario_id, created_at) VALUES (?, NOW())');
        $stmt->execute([$userId]);

        $user = [
            'id'       => $userId,
            'nome'     => $data['nome'],
            'email'    => $data['email'],
            'telefone' => $data['telefone'],
            'role'     => 'cliente',
        ];

        $token = AuthService::generateToken($user);
        $refreshToken = AuthService::generateRefreshToken($user);

        Response::created([
            'token'         => $token,
            'refresh_token' => $refreshToken,
            'usuario'       => $user,
        ], 'Conta criada com sucesso.');
    }

    public function refresh(array $params, array $body): void
    {
        $refreshToken = $body['refresh_token'] ?? '';

        if (empty($refreshToken)) {
            Response::error('Refresh token é obrigatório.', 400);
        }

        $payload = AuthService::validateRefreshToken($refreshToken);
        if (!$payload) {
            Response::unauthorized('Refresh token inválido ou expirado.');
        }

        $db = Connection::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ? AND ativo = 1');
        $stmt->execute([$payload['sub']]);
        $user = $stmt->fetch();

        if (!$user) {
            Response::unauthorized('Usuário não encontrado.');
        }

        unset($user['senha']);

        $newToken = AuthService::generateToken($user);
        $newRefresh = AuthService::generateRefreshToken($user);

        Response::success([
            'token'         => $newToken,
            'refresh_token' => $newRefresh,
        ]);
    }

    public function logout(array $params, array $body): void
    {
        // Em JWT stateless, o logout é feito no client (remover token).
        // Aqui poderíamos adicionar blacklist futuramente.
        Response::success(null, 'Logout realizado.');
    }

    public function me(array $params, array $body): void
    {
        $user = AuthMiddleware::getUser();
        Response::success($user);
    }
}
