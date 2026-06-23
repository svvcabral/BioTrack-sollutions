<?php

require_once __DIR__ . '/includes/funcoes.php';

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

/*
 * Login temporário da Ficha 10.
 * Na ligação à base de dados será substituído por password_verify().
 */
if ($username !== 'admin@biotrack.pt' || $password !== '123456') {
    $_SESSION['server_error'] = 'Email ou palavra-passe incorretos.';
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

session_regenerate_id(true);

$_SESSION['utilizador'] = $username;
$_SESSION['perfil'] = 'administrador';

header('Location: ' . BASE_URL . '/private/dashboard.php');
exit;