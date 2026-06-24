<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_autenticado();
permitir_apenas_get_post();

$pageTitle = 'Alterar Palavra-passe';
$activePage = '';
$erros = [];
$sucesso = false;

iniciar_sessao();

if (empty($_SESSION['csrf_alterar_palavra_passe'])) {
    $_SESSION['csrf_alterar_palavra_passe'] = bin2hex(random_bytes(32));
}

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    $palavra_passe_atual = $_POST['palavra_passe_atual'] ?? '';
    $nova_palavra_passe = $_POST['nova_palavra_passe'] ?? '';
    $confirmacao = $_POST['confirmacao_palavra_passe'] ?? '';

    if (!hash_equals($_SESSION['csrf_alterar_palavra_passe'], $csrf_token)) {
        $erros[] = 'O pedido expirou. Atualiza a página e tenta novamente.';
    }

    if ($palavra_passe_atual === '') {
        $erros[] = 'A palavra-passe atual é obrigatória.';
    }

    if (strlen($nova_palavra_passe) < 8 || strlen($nova_palavra_passe) > 72) {
        $erros[] = 'A nova palavra-passe deve ter entre 8 e 72 caracteres.';
    }

    if ($nova_palavra_passe !== $confirmacao) {
        $erros[] = 'A confirmação não corresponde à nova palavra-passe.';
    }

    if ($palavra_passe_atual !== '' && hash_equals($palavra_passe_atual, $nova_palavra_passe)) {
        $erros[] = 'A nova palavra-passe deve ser diferente da atual.';
    }

    if (empty($erros)) {
        try {
            $ligacao = ligar_bd();
            $stmt = $ligacao->prepare(
                'SELECT palavra_passe
                 FROM utilizadores
                 WHERE id_utilizador = :id_utilizador AND ativo = 1
                 LIMIT 1'
            );
            $stmt->execute([
                ':id_utilizador' => (int) $_SESSION['id_utilizador']
            ]);
            $utilizador = $stmt->fetch();

            if (!$utilizador || !password_verify($palavra_passe_atual, $utilizador['palavra_passe'])) {
                $erros[] = 'A palavra-passe atual está incorreta.';
            } else {
                $stmt = $ligacao->prepare(
                    'UPDATE utilizadores
                     SET palavra_passe = :palavra_passe
                     WHERE id_utilizador = :id_utilizador'
                );
                $stmt->execute([
                    ':palavra_passe' => password_hash($nova_palavra_passe, PASSWORD_DEFAULT),
                    ':id_utilizador' => (int) $_SESSION['id_utilizador']
                ]);

                $sucesso = true;
                $_SESSION['csrf_alterar_palavra_passe'] = bin2hex(random_bytes(32));
            }
        } catch (PDOException $erro) {
            $erros[] = 'Não foi possível alterar a palavra-passe.';
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container my-5">
    <div class="mx-auto" style="max-width: 680px;">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-1">Alterar palavra-passe</h1>
            <p class="text-muted mb-0">Confirma a palavra-passe atual antes de definires uma nova.</p>
        </div>

        <?php if ($sucesso): ?>
            <div class="alert alert-success">
                A palavra-passe foi alterada com sucesso.
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <strong>Não foi possível alterar a palavra-passe:</strong>
                <ul class="mb-0">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= h($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="alterar_palavra_passe.php" class="card border-0 shadow-sm">
            <input type="hidden" name="csrf_token"
                   value="<?= h($_SESSION['csrf_alterar_palavra_passe']) ?>">
            <div class="card-body p-4">
                <div class="mb-3">
                    <label for="palavra_passe_atual" class="form-label fw-bold">Palavra-passe atual</label>
                    <input type="password" id="palavra_passe_atual" name="palavra_passe_atual"
                           class="form-control" autocomplete="current-password" required>
                </div>

                <div class="mb-3">
                    <label for="nova_palavra_passe" class="form-label fw-bold">Nova palavra-passe</label>
                    <input type="password" id="nova_palavra_passe" name="nova_palavra_passe"
                           class="form-control" minlength="8" maxlength="72"
                           autocomplete="new-password" required>
                    <div class="form-text">Deve ter entre 8 e 72 caracteres.</div>
                </div>

                <div class="mb-4">
                    <label for="confirmacao_palavra_passe" class="form-label fw-bold">
                        Confirmar nova palavra-passe
                    </label>
                    <input type="password" id="confirmacao_palavra_passe"
                           name="confirmacao_palavra_passe" class="form-control"
                           minlength="8" maxlength="72" autocomplete="new-password" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="dashboard.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-2"></i>Guardar palavra-passe
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
