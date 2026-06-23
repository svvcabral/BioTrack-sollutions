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