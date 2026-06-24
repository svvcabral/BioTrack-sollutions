<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_administrador();

$pageTitle = 'Arquivar Localização';
$activePage = 'localizacoes';

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$id_encriptado = trim($_GET['id'] ?? '');
$id_localizacao = aes_decrypt($id_encriptado);

if ($id_localizacao === false || !ctype_digit((string) $id_localizacao)) {
    header('Location: localizacoes.php?erro=localizacao_invalida');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare(
        'SELECT edificio, piso, servico, sala
         FROM localizacoes
         WHERE id_localizacao = :id_localizacao AND ativo = 1'
    );
    $stmt->execute([':id_localizacao' => (int) $id_localizacao]);
    $localizacao = $stmt->fetch();

    if (!$localizacao) {
        header('Location: localizacoes.php?erro=nao_encontrada');
        exit;
    }
} catch (PDOException $erro) {
    header('Location: localizacoes.php?erro=arquivar');
    exit;
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container my-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-danger mb-3"><i class="fas fa-archive fa-2x"></i></div>
            <h1 class="h3 fw-bold">Arquivar localização?</h1>
            <p class="text-muted">Só é possível arquivar uma localização sem equipamentos ativos associados.</p>

            <dl class="row bg-light rounded p-3 mb-4">
                <dt class="col-sm-4">Serviço</dt>
                <dd class="col-sm-8"><?= h($localizacao['servico']) ?></dd>
                <dt class="col-sm-4">Edifício / Piso</dt>
                <dd class="col-sm-8"><?= h($localizacao['edificio'] . ' - ' . $localizacao['piso']) ?></dd>
                <dt class="col-sm-4">Sala</dt>
                <dd class="col-sm-8 mb-0"><?= h((string) ($localizacao['sala'] ?: 'Sem sala')) ?></dd>
            </dl>

            <form action="localizacao_apagar.php" method="post" class="d-flex justify-content-end gap-2">
                <input type="hidden" name="id" value="<?= h($id_encriptado) ?>">
                <a href="localizacoes.php" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-archive me-2"></i>Confirmar arquivo
                </button>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
