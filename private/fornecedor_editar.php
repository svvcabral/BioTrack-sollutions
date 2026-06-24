<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/validacoes.php';

redirecionar_se_nao_administrador();
permitir_apenas_get_post();

$pageTitle = 'Editar Fornecedor';
$activePage = 'fornecedores';
$erros = [];
$tipos = ['fabricante', 'distribuidor', 'assistencia_tecnica', 'consumiveis'];

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function texto_tipo(string $tipo): string
{
    $textos = [
        'assistencia_tecnica' => 'Assistência técnica',
        'consumiveis' => 'Consumíveis',
    ];
    return $textos[$tipo] ?? ucfirst($tipo);
}

$id_encriptado = trim($_GET['id'] ?? '');
$id_fornecedor = aes_decrypt($id_encriptado);
if ($id_fornecedor === false || !ctype_digit((string) $id_fornecedor)) {
    header('Location: fornecedores.php?erro=fornecedor_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare('SELECT * FROM fornecedores WHERE id_fornecedor = :id_fornecedor');
    $stmt->execute([':id_fornecedor' => (int) $id_fornecedor]);
    $fornecedor = $stmt->fetch();
    if (!$fornecedor) {
        header('Location: fornecedores.php?erro=nao_encontrado');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $campos = [
            'nome', 'nif', 'telefone', 'email', 'morada', 'website',
            'pessoa_contacto', 'telefone_contacto', 'tipo', 'observacoes',
        ];
        foreach ($campos as $campo) {
            $fornecedor[$campo] = trim($_POST[$campo] ?? '');
        }

        $erros = validar_fornecedor($fornecedor);

        if (empty($erros)) {
            $stmt = $ligacao->prepare(
                'UPDATE fornecedores SET
                    nome = :nome, nif = :nif, telefone = :telefone, email = :email,
                    morada = :morada, website = :website, pessoa_contacto = :pessoa_contacto,
                    telefone_contacto = :telefone_contacto, tipo = :tipo, observacoes = :observacoes
                 WHERE id_fornecedor = :id_fornecedor'
            );
            $stmt->execute([
                ':nome' => $fornecedor['nome'],
                ':nif' => $fornecedor['nif'],
                ':telefone' => $fornecedor['telefone'] ?: null,
                ':email' => strtolower($fornecedor['email']),
                ':morada' => $fornecedor['morada'] ?: null,
                ':website' => $fornecedor['website'] ?: null,
                ':pessoa_contacto' => $fornecedor['pessoa_contacto'] ?: null,
                ':telefone_contacto' => $fornecedor['telefone_contacto'] ?: null,
                ':tipo' => $fornecedor['tipo'],
                ':observacoes' => $fornecedor['observacoes'] ?: null,
                ':id_fornecedor' => (int) $id_fornecedor,
            ]);
            header('Location: fornecedores.php?atualizado=1');
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
    <a href="fornecedores.php" class="text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i>Voltar aos fornecedores
    </a>
    <h1 class="h2 fw-bold my-3">Editar Fornecedor</h1>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= h($erro) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" action="fornecedor_editar.php?id=<?= h($id_encriptado) ?>" class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label fw-bold">Nome da empresa</label><input name="nome" class="form-control" value="<?= h($fornecedor['nome']) ?>" required></div>
                <div class="col-md-4"><label class="form-label fw-bold">NIF</label><input name="nif" class="form-control" value="<?= h($fornecedor['nif']) ?>" required></div>
                <div class="col-md-4"><label class="form-label fw-bold">Telefone</label><input name="telefone" class="form-control" value="<?= h((string) $fornecedor['telefone']) ?>"></div>
                <div class="col-md-8"><label class="form-label fw-bold">Email</label><input type="email" name="email" class="form-control" value="<?= h((string) $fornecedor['email']) ?>" required></div>
                <div class="col-md-6"><label class="form-label fw-bold">Website</label><input name="website" class="form-control" value="<?= h((string) $fornecedor['website']) ?>"></div>
                <div class="col-md-6"><label class="form-label fw-bold">Tipo</label><select name="tipo" class="form-select"><?php foreach ($tipos as $tipo): ?><option value="<?= h($tipo) ?>" <?= $fornecedor['tipo'] === $tipo ? 'selected' : '' ?>><?= h(texto_tipo($tipo)) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label fw-bold">Pessoa de contacto</label><input name="pessoa_contacto" class="form-control" value="<?= h((string) $fornecedor['pessoa_contacto']) ?>"></div>
                <div class="col-md-6"><label class="form-label fw-bold">Telefone de contacto</label><input name="telefone_contacto" class="form-control" value="<?= h((string) $fornecedor['telefone_contacto']) ?>"></div>
                <div class="col-12"><label class="form-label fw-bold">Morada</label><input name="morada" class="form-control" value="<?= h((string) $fornecedor['morada']) ?>"></div>
                <div class="col-12"><label class="form-label fw-bold">Observações</label><textarea name="observacoes" class="form-control" rows="3"><?= h((string) $fornecedor['observacoes']) ?></textarea></div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <a href="fornecedores.php" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Guardar alterações</button>
        </div>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
