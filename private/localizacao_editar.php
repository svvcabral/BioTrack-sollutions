<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/validacoes.php';

redirecionar_se_nao_autenticado();
permitir_apenas_get_post();
validar_csrf_post();

$pageTitle = 'Editar Localização';
$activePage = 'localizacoes';
$erros = [];

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
    $stmt = $ligacao->prepare('SELECT * FROM localizacoes WHERE id_localizacao = :id_localizacao');
    $stmt->execute([':id_localizacao' => (int) $id_localizacao]);
    $localizacao = $stmt->fetch();
    if (!$localizacao) {
        header('Location: localizacoes.php?erro=nao_encontrada');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach (['servico', 'edificio', 'piso', 'sala'] as $campo) {
            $localizacao[$campo] = trim($_POST[$campo] ?? '');
        }

        $erros = validar_localizacao($localizacao);

        if (empty($erros)) {
            $stmt = $ligacao->prepare(
                'UPDATE localizacoes SET
                    edificio = :edificio, piso = :piso,
                    servico = :servico, sala = :sala
                 WHERE id_localizacao = :id_localizacao'
            );
            $stmt->execute([
                ':edificio' => $localizacao['edificio'],
                ':piso' => $localizacao['piso'],
                ':servico' => $localizacao['servico'],
                ':sala' => $localizacao['sala'] ?: null,
                ':id_localizacao' => (int) $id_localizacao,
            ]);
            header('Location: localizacoes.php?atualizada=1');
            exit;
        }
    }
} catch (PDOException $erro) {
    $erros[] = 'Não foi possível concluir a operação: ' . $erro->getMessage();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container my-5">
    <a href="localizacoes.php" class="text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i>Voltar às localizações
    </a>
    <h1 class="h2 fw-bold my-3">Editar Localização</h1>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= h($erro) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" action="localizacao_editar.php?id=<?= h($id_encriptado) ?>" class="card border-0 shadow-sm">
        <?= campo_csrf() ?>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12"><label class="form-label fw-bold">Serviço / Departamento</label><input name="servico" class="form-control" value="<?= h($localizacao['servico']) ?>" required></div>
                <div class="col-md-6"><label class="form-label fw-bold">Edifício</label><input name="edificio" class="form-control" value="<?= h($localizacao['edificio']) ?>" required></div>
                <div class="col-md-3"><label class="form-label fw-bold">Piso</label><input name="piso" class="form-control" value="<?= h($localizacao['piso']) ?>" required></div>
                <div class="col-md-3"><label class="form-label fw-bold">Sala</label><input name="sala" class="form-control" value="<?= h((string) $localizacao['sala']) ?>"></div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <a href="localizacoes.php" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Guardar alterações</button>
        </div>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
