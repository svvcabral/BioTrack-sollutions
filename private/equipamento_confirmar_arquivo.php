<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_administrador();

$pageTitle = 'Arquivar Equipamento';
$activePage = 'equipamentos';

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$id_encriptado = trim($_GET['id'] ?? '');
$id_equipamento = aes_decrypt($id_encriptado);

if ($id_equipamento === false || !ctype_digit((string) $id_equipamento)) {
    header('Location: equipamentos.php?erro=equipamento_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare(
        'SELECT codigo_interno, designacao, marca, modelo
         FROM equipamentos
         WHERE id_equipamento = :id_equipamento AND ativo = 1'
    );
    $stmt->execute([':id_equipamento' => (int) $id_equipamento]);
    $equipamento = $stmt->fetch();

    if (!$equipamento) {
        header('Location: equipamentos.php?erro=nao_encontrado');
        exit;
    }
} catch (PDOException $erro) {
    header('Location: equipamentos.php?erro=arquivo');
    exit;
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container my-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-danger mb-3"><i class="fas fa-archive fa-2x"></i></div>
            <h1 class="h3 fw-bold">Arquivar equipamento?</h1>
            <p class="text-muted">Confirma os dados antes de continuar. O registo ficará inativo, sem ser eliminado da base de dados.</p>

            <dl class="row bg-light rounded p-3 mb-4">
                <dt class="col-sm-4">Código interno</dt>
                <dd class="col-sm-8"><?= h($equipamento['codigo_interno']) ?></dd>
                <dt class="col-sm-4">Designação</dt>
                <dd class="col-sm-8"><?= h($equipamento['designacao']) ?></dd>
                <dt class="col-sm-4">Marca / Modelo</dt>
                <dd class="col-sm-8 mb-0"><?= h($equipamento['marca'] . ' ' . $equipamento['modelo']) ?></dd>
            </dl>

            <form action="equipamento_apagar.php" method="post" class="d-flex justify-content-end gap-2">
                <input type="hidden" name="id" value="<?= h($id_encriptado) ?>">
                <a href="equipamentos.php" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-archive me-2"></i>Confirmar arquivo
                </button>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
