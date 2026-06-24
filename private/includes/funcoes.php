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
