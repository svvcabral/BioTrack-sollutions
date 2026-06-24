<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_administrador();
validar_csrf_post();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: equipamentos.php');
    exit;
}

$id_encriptado = trim($_POST['id'] ?? '');
$id_equipamento = aes_decrypt($id_encriptado);

if ($id_equipamento === false || !ctype_digit((string) $id_equipamento)) {
    header('Location: equipamentos.php?erro=equipamento_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();

    $stmt = $ligacao->prepare(
        'UPDATE equipamentos
         SET ativo = 0
         WHERE id_equipamento = :id_equipamento AND ativo = 1'
    );

    $stmt->execute([
        ':id_equipamento' => (int) $id_equipamento
    ]);

    registar_log($ligacao, 'arquivar_equipamento', 'equipamentos', (int) $id_equipamento);
    header('Location: equipamentos.php?apagado=1');
    exit;
} catch (PDOException $erro) {
    header('Location: equipamentos.php?erro=apagar');
    exit;
}
