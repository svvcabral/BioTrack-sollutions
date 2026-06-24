<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: localizacoes.php');
    exit;
}

$id_encriptado = trim($_POST['id'] ?? '');
$id_localizacao = aes_decrypt($id_encriptado);
if ($id_localizacao === false || !ctype_digit((string) $id_localizacao)) {
    header('Location: localizacoes.php?erro=localizacao_invalida');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare(
        'SELECT COUNT(*) FROM equipamentos
         WHERE id_localizacao = :id_localizacao AND ativo = 1'
    );
    $stmt->execute([':id_localizacao' => (int) $id_localizacao]);

    if ((int) $stmt->fetchColumn() > 0) {
        header('Location: localizacoes.php?erro=com_equipamentos');
        exit;
    }

    $stmt = $ligacao->prepare(
        'UPDATE localizacoes
         SET ativo = 0
         WHERE id_localizacao = :id_localizacao AND ativo = 1'
    );
    $stmt->execute([':id_localizacao' => (int) $id_localizacao]);
    header('Location: localizacoes.php?arquivada=1');
    exit;
} catch (PDOException $erro) {
    header('Location: localizacoes.php?erro=arquivar');
    exit;
}
