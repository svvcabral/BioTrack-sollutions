<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_administrador();

$pageTitle = 'Arquivar Fornecedor';
$activePage = 'fornecedores';

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$id_encriptado = trim($_GET['id'] ?? '');
$id_fornecedor = aes_decrypt($id_encriptado);

if ($id_fornecedor === false || !ctype_digit((string) $id_fornecedor)) {
    header('Location: fornecedores.php?erro=fornecedor_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare(
        'SELECT nome, nif, email
         FROM fornecedores
         WHERE id_fornecedor = :id_fornecedor AND ativo = 1'
    );
    $stmt->execute([':id_fornecedor' => (int) $id_fornecedor]);
    $fornecedor = $stmt->fetch();

    if (!$fornecedor) {
        header('Location: fornecedores.php?erro=nao_encontrado');
        exit;
    }
} catch (PDOException $erro) {
    header('Location: fornecedores.php?erro=arquivar');
    exit;
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container my-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-danger mb-3"><i class="fas fa-archive fa-2x"></i></div>
            <h1 class="h3 fw-bold">Arquivar fornecedor?</h1>
            <p class="text-muted">O fornecedor ficará inativo, mas o seu histórico será preservado.</p>

            <dl class="row bg-light rounded p-3 mb-4">
                <dt class="col-sm-4">Empresa</dt>
                <dd class="col-sm-8"><?= h($fornecedor['nome']) ?></dd>
                <dt class="col-sm-4">NIF</dt>
                <dd class="col-sm-8"><?= h($fornecedor['nif']) ?></dd>
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8 mb-0"><?= h((string) $fornecedor['email']) ?></dd>
            </dl>

            <form action="fornecedor_apagar.php" method="post" class="d-flex justify-content-end gap-2">
                <?= campo_csrf() ?>
                <input type="hidden" name="id" value="<?= h($id_encriptado) ?>">
                <a href="fornecedores.php" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-archive me-2"></i>Confirmar arquivo
                </button>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
