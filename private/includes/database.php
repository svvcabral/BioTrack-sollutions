<?php

require_once __DIR__ . '/../../config/config.php';

function ligar_bd(): PDO
{
    static $ligacao = null;

    if ($ligacao instanceof PDO) {
        return $ligacao;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    $opcoes = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $ligacao = new PDO($dsn, DB_USER, DB_PASSWORD, $opcoes);

    return $ligacao;
}