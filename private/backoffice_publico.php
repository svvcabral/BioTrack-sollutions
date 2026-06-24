<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_administrador();

$pageTitle = 'Portal Público';
$activePage = 'portal';
$erros = [];
$sucesso = false;

iniciar_sessao();

if (empty($_SESSION['csrf_portal_publico'])) {
    $_SESSION['csrf_portal_publico'] = bin2hex(random_bytes(32));
}

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$campos = [
    'hero_titulo' => ['chave' => 'hero', 'parte' => 'titulo', 'padrao' => 'A nova era da gestão de Tecnologia Médica'],
    'hero_texto' => ['chave' => 'hero', 'parte' => 'conteudo', 'padrao' => 'Mapeamento em tempo real, gestão de ciclo de vida e mitigação de falhas para dispositivos médicos de suporte crítico.'],
    'visao_titulo' => ['chave' => 'visao', 'parte' => 'titulo', 'padrao' => 'Da Engenharia Biomédica para a Prática Clínica'],
    'visao_texto' => ['chave' => 'visao', 'parte' => 'conteudo', 'padrao' => 'Como estudante de Engenharia Biomédica no ISEP, desenhei o BioTrack como uma ponte entre a tecnologia e o cuidado ao paciente.'],
    'autor_nome' => ['chave' => 'autor_nome', 'parte' => 'conteudo', 'padrao' => 'Sofia'],
    'autor_papel' => ['chave' => 'autor_papel', 'parte' => 'conteudo', 'padrao' => 'Autora do Projeto • SIBDAS 2026'],
    'contacto_email' => ['chave' => 'contacto_email', 'parte' => 'conteudo', 'padrao' => 'suporte@biotrack.pt'],
    'contacto_telefone' => ['chave' => 'contacto_telefone', 'parte' => 'conteudo', 'padrao' => '+351 228 340 500'],
    'contacto_morada' => ['chave' => 'contacto_morada', 'parte' => 'conteudo', 'padrao' => "Rua Dr. António Bernardino de Almeida\n4200-072, Porto\nPortugal"],
    'horario_semana' => ['chave' => 'horario_semana', 'parte' => 'conteudo', 'padrao' => '2ª a 6ª Feira: 09h — 17h'],
    'horario_sabado' => ['chave' => 'horario_sabado', 'parte' => 'conteudo', 'padrao' => 'Sábados: 09h — 13h'],
    'horario_domingo' => ['chave' => 'horario_domingo', 'parte' => 'conteudo', 'padrao' => 'Domingos / Feriados: Encerrado'],
];

$valores = array_map(static fn(array $campo): string => $campo['padrao'], $campos);

try {
    $ligacao = ligar_bd();
    $registos = $ligacao->query(
        'SELECT chave, titulo, conteudo FROM conteudos_publicos'
    )->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

    foreach ($campos as $nome => $configuracao) {
        $chave = $configuracao['chave'];
        $parte = $configuracao['parte'];
        if (isset($registos[$chave][$parte]) && $registos[$chave][$parte] !== '') {
            $valores[$nome] = $registos[$chave][$parte];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_portal_publico'], $token)) {
            $erros[] = 'O pedido expirou. Atualiza a página e tenta novamente.';
        }

        foreach ($campos as $nome => $configuracao) {
            $valores[$nome] = trim($_POST[$nome] ?? '');
            if ($valores[$nome] === '') {
                $erros[] = 'Todos os campos do portal público são obrigatórios.';
                break;
            }
        }

        if (!filter_var($valores['contacto_email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'O email de suporte não é válido.';
        }

        if (empty($erros)) {
            $ligacao->beginTransaction();
            $stmt = $ligacao->prepare(
                'INSERT INTO conteudos_publicos (
                    chave, titulo, conteudo, atualizado_por
                 ) VALUES (
                    :chave, :titulo, :conteudo, :atualizado_por
                 )
                 ON DUPLICATE KEY UPDATE
                    titulo = VALUES(titulo),
                    conteudo = VALUES(conteudo),
                    atualizado_por = VALUES(atualizado_por)'
            );

            $agrupados = [];
            foreach ($campos as $nome => $configuracao) {
                $chave = $configuracao['chave'];
                $agrupados[$chave][$configuracao['parte']] = $valores[$nome];
            }

            foreach ($agrupados as $chave => $conteudo) {
                $stmt->execute([
                    ':chave' => $chave,
                    ':titulo' => $conteudo['titulo'] ?? null,
                    ':conteudo' => $conteudo['conteudo'] ?? '',
                    ':atualizado_por' => (int) $_SESSION['id_utilizador'],
                ]);
            }

            $ligacao->commit();
            registar_log($ligacao, 'atualizar_portal_publico', 'conteudos_publicos', null, 'Conteúdos públicos atualizados.');
            $_SESSION['csrf_portal_publico'] = bin2hex(random_bytes(32));
            $sucesso = true;
        }
    }
} catch (PDOException $erro) {
    if (isset($ligacao) && $ligacao->inTransaction()) {
        $ligacao->rollBack();
    }
    $erros[] = 'Não foi possível carregar ou guardar os conteúdos do portal.';
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    
    
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Gestão do Portal Público</h1>
                <p class="text-muted mb-0">Edite os textos e informações apresentados na página inicial (Front Office)</p>
            </div>
            <a href="../public/index.php" target="_blank" class="btn btn-outline-secondary fw-bold">
                <i class="fas fa-external-link-alt me-2"></i>Ver Site Público
            </a>
        </div>

        <?php if ($sucesso): ?>
            <div class="alert alert-success">Os conteúdos do portal público foram atualizados com sucesso.</div>
        <?php endif; ?>
        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= h($erro) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" action="backoffice_publico.php">
            <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_portal_publico']) ?>">
            <div class="row g-4">
                <div class="col-lg-8">
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-heading me-2 text-primary"></i>Banner Principal (Hero Section)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Título Principal</label>
                                <input type="text" name="hero_titulo" class="form-control form-custom-input" value="<?= h($valores['hero_titulo']) ?>" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-dark">Subtítulo / Descrição</label>
                                <textarea name="hero_texto" class="form-control form-custom-input" rows="3" required><?= h($valores['hero_texto']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-quote-left me-2 text-primary"></i>Secção "A Visão"</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Título da secção</label>
                                <input type="text" name="visao_titulo" class="form-control form-custom-input" value="<?= h($valores['visao_titulo']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Texto do Manifesto</label>
                                <textarea name="visao_texto" class="form-control form-custom-input" rows="4" required><?= h($valores['visao_texto']) ?></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Nome do Autor</label>
                                    <input type="text" name="autor_nome" class="form-control form-custom-input" value="<?= h($valores['autor_nome']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Cargo / Papel</label>
                                    <input type="text" name="autor_papel" class="form-control form-custom-input" value="<?= h($valores['autor_papel']) ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-address-card me-2 text-primary"></i>Contactos e Rodapé</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Email de Suporte</label>
                                <input type="email" name="contacto_email" class="form-control form-custom-input" value="<?= h($valores['contacto_email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Telefone Geral</label>
                                <input type="text" name="contacto_telefone" class="form-control form-custom-input" value="<?= h($valores['contacto_telefone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Morada</label>
                                <textarea name="contacto_morada" class="form-control form-custom-input" rows="3" required><?= h($valores['contacto_morada']) ?></textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-dark">Horário durante a semana</label>
                                <input type="text" name="horario_semana" class="form-control form-custom-input" value="<?= h($valores['horario_semana']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Horário de sábado</label>
                                <input type="text" name="horario_sabado" class="form-control form-custom-input" value="<?= h($valores['horario_sabado']) ?>" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-dark">Domingos e feriados</label>
                                <input type="text" name="horario_domingo" class="form-control form-custom-input" value="<?= h($valores['horario_domingo']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Alterações
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
