<?php

require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/database.php';

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$username = trim($_POST['text_username'] ?? '');
$password = $_POST['text_password'] ?? '';

$erros = [];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Introduza um endereço de email válido.';
}

if (strlen($password) < 6 || strlen($password) > 72) {
    $erros[] = 'A palavra-passe deve ter entre 6 e 72 caracteres.';
}

if (!empty($erros)) {
    $_SESSION['validation_errors'] = $erros;
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

try {
    $ligacao = ligar_bd();

    $sql = 'SELECT id_utilizador, nome, email, palavra_passe, perfil
            FROM utilizadores
            WHERE email = :email AND ativo = TRUE
            LIMIT 1';

    $stmt = $ligacao->prepare($sql);
    $stmt->bindValue(':email', $username);
    $stmt->execute();

    $utilizador = $stmt->fetch();

    if (!$utilizador || !password_verify($password, $utilizador['palavra_passe'])) {
        $_SESSION['server_error'] = 'Email ou palavra-passe incorretos.';
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }

    $stmt = $ligacao->prepare(
        'UPDATE utilizadores
         SET ultimo_login = NOW()
         WHERE id_utilizador = :id_utilizador'
    );
    $stmt->execute([
        ':id_utilizador' => (int) $utilizador['id_utilizador']
    ]);
} catch (PDOException $erro) {
    $_SESSION['server_error'] = 'Não foi possível ligar à base de dados.';
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

session_regenerate_id(true);

$_SESSION['id_utilizador'] = $utilizador['id_utilizador'];
$_SESSION['utilizador'] = $utilizador['email'];
$_SESSION['nome_utilizador'] = $utilizador['nome'];
$_SESSION['perfil'] = $utilizador['perfil'];

header('Location: ' . BASE_URL . '/private/dashboard.php');
exit;
