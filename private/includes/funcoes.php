<?php

require_once __DIR__ . '/../../config/config.php';

function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function utilizador_autenticado(): bool
{
    iniciar_sessao();

    return isset($_SESSION['utilizador']);
}

function redirecionar_se_nao_autenticado(): void
{
    if (!utilizador_autenticado()) {
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }
}

function utilizador_administrador(): bool
{
    iniciar_sessao();

    return ($_SESSION['perfil'] ?? '') === 'administrador';
}

function redirecionar_se_nao_administrador(): void
{
    redirecionar_se_nao_autenticado();

    if (!utilizador_administrador()) {
        header('Location: ' . BASE_URL . '/private/dashboard.php?erro=sem_permissao');
        exit;
    }
}

function aes_encrypt($valor): string
{
    $encriptado = openssl_encrypt(
        (string) $valor,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );

    return $encriptado === false ? '' : bin2hex($encriptado);
}

function aes_decrypt($valor)
{
    if (!is_string($valor) || $valor === '' || strlen($valor) % 2 !== 0 || !ctype_xdigit($valor)) {
        return false;
    }

    $binario = hex2bin($valor);
    if ($binario === false) {
        return false;
    }

    return openssl_decrypt(
        $binario,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}

function permitir_apenas_get_post(): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }
}

function registar_log(PDO $ligacao, string $evento, ?string $entidade = null, ?int $id_registo = null, ?string $detalhes = null): void
{
    iniciar_sessao();

    try {
        $stmt = $ligacao->prepare(
            'INSERT INTO logs (
                id_utilizador, evento, entidade, id_registo, detalhes, endereco_ip
             ) VALUES (
                :id_utilizador, :evento, :entidade, :id_registo, :detalhes, :endereco_ip
             )'
        );
        $stmt->execute([
            ':id_utilizador' => isset($_SESSION['id_utilizador']) ? (int) $_SESSION['id_utilizador'] : null,
            ':evento' => $evento,
            ':entidade' => $entidade,
            ':id_registo' => $id_registo,
            ':detalhes' => $detalhes,
            ':endereco_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (PDOException $erro) {
        error_log('Falha ao registar evento: ' . $erro->getMessage());
    }
}

function token_csrf(): string
{
    iniciar_sessao();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function campo_csrf(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(token_csrf(), ENT_QUOTES, 'UTF-8')
        . '">';
}

function validar_csrf_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    iniciar_sessao();
    $recebido = $_POST['csrf_token'] ?? '';

    if (!is_string($recebido) || !hash_equals(token_csrf(), $recebido)) {
        http_response_code(403);
        exit('Pedido inválido ou expirado. Volta à página anterior e tenta novamente.');
    }
}
