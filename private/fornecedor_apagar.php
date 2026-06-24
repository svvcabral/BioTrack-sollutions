<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_autenticado();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fornecedores.php');
    exit;
}

$id_fornecedor = trim($_POST['id_fornecedor'] ?? '');

if ($id_fornecedor === '' || !ctype_digit($id_fornecedor)) {
    header('Location: fornecedores.php?erro=fornecedor_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare(
        'UPDATE fornecedores SET ativo = 0 WHERE id_fornecedor = :id_fornecedor'
    );
    $stmt->execute([':id_fornecedor' => (int) $id_fornecedor]);
    header('Location: fornecedores.php?arquivado=1');
    exit;
} catch (PDOException $erro) {
    header('Location: fornecedores.php?erro=arquivar');
    exit;
}
