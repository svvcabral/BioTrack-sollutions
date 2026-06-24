<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_autenticado();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: equipamentos.php');
    exit;
}

$id_equipamento = trim($_POST['id_equipamento'] ?? '');

if ($id_equipamento === '' || !ctype_digit($id_equipamento)) {
    header('Location: equipamentos.php?erro=equipamento_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();

    $stmt = $ligacao->prepare(
        'UPDATE equipamentos
         SET ativo = 0
         WHERE id_equipamento = :id_equipamento'
    );

    $stmt->execute([
        ':id_equipamento' => (int) $id_equipamento
    ]);

    header('Location: equipamentos.php?apagado=1');
    exit;
} catch (PDOException $erro) {
    header('Location: equipamentos.php?erro=apagar');
    exit;
}