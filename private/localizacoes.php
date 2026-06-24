<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/validacoes.php';

redirecionar_se_nao_autenticado();
validar_csrf_post();

$pageTitle = 'Localizações';
$activePage = 'localizacoes';
$erros = [];
$erro_sistema = '';
$valores = [
    'servico' => '',
    'edificio' => '',
    'piso' => '',
    'sala' => '',
];
$filtros = [
    'pesquisa' => trim($_GET['pesquisa'] ?? ''),
    'edificio' => trim($_GET['edificio'] ?? ''),
    'piso' => trim($_GET['piso'] ?? ''),
    'estado' => trim($_GET['estado'] ?? ''),
    'ordenacao' => trim($_GET['ordenacao'] ?? 'servico_asc'),
];

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

try {
    $ligacao = ligar_bd();
} catch (PDOException $err) {
    $ligacao = null;
    $erro_sistema = 'Erro ao ligar à base de dados: ' . $err->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ligacao instanceof PDO) {
    foreach ($valores as $campo => $valor) {
        $valores[$campo] = trim($_POST[$campo] ?? '');
    }

    $erros = validar_localizacao($valores);

    if (empty($erros)) {
        $valores['servico'] = ucwords(strtolower($valores['servico']));
        $valores['edificio'] = ucwords(strtolower($valores['edificio']));
        $valores['piso'] = ucwords(strtolower($valores['piso']));
        $valores['sala'] = strtoupper($valores['sala']);

        try {
            $sql = "INSERT INTO localizacoes (edificio, piso, servico, sala)
                    VALUES (:edificio, :piso, :servico, :sala)";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':edificio' => $valores['edificio'],
                ':piso' => $valores['piso'],
                ':servico' => $valores['servico'],
                ':sala' => $valores['sala'] !== '' ? $valores['sala'] : null,
            ]);

            header('Location: localizacoes.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = 'Erro ao gravar os dados: ' . $err->getMessage();
        }
    }
}

$localizacoes = [];
$edificios = [];
$pisos = [];
if ($ligacao instanceof PDO) {
    try {
        $edificios = $ligacao->query(
            'SELECT DISTINCT edificio FROM localizacoes ORDER BY edificio'
        )->fetchAll(PDO::FETCH_COLUMN);
        $pisos = $ligacao->query(
            'SELECT DISTINCT piso FROM localizacoes ORDER BY piso'
        )->fetchAll(PDO::FETCH_COLUMN);

        $condicoes = [];
        $parametros = [];

        if ($filtros['pesquisa'] !== '') {
            $condicoes[] = '(servico LIKE :pesquisa_servico
                             OR edificio LIKE :pesquisa_edificio
                             OR piso LIKE :pesquisa_piso
                             OR sala LIKE :pesquisa_sala)';
            $termo = '%' . $filtros['pesquisa'] . '%';
            $parametros[':pesquisa_servico'] = $termo;
            $parametros[':pesquisa_edificio'] = $termo;
            $parametros[':pesquisa_piso'] = $termo;
            $parametros[':pesquisa_sala'] = $termo;
        }

        if ($filtros['edificio'] !== '') {
            $condicoes[] = 'edificio = :edificio';
            $parametros[':edificio'] = $filtros['edificio'];
        }

        if ($filtros['piso'] !== '') {
            $condicoes[] = 'piso = :piso';
            $parametros[':piso'] = $filtros['piso'];
        }

        if ($filtros['estado'] === 'ativo') {
            $condicoes[] = 'ativo = 1';
        } elseif ($filtros['estado'] === 'inativo') {
            $condicoes[] = 'ativo = 0';
        }

        $ordenacoes = [
            'servico_asc' => 'servico ASC',
            'servico_desc' => 'servico DESC',
            'edificio_asc' => 'edificio ASC, piso ASC, servico ASC',
            'piso_asc' => 'piso ASC, servico ASC',
        ];
        $ordem = $ordenacoes[$filtros['ordenacao']] ?? $ordenacoes['servico_asc'];

        $sql = 'SELECT id_localizacao, edificio, piso, servico, sala, ativo FROM localizacoes';
        if (!empty($condicoes)) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }
        $sql .= ' ORDER BY ' . $ordem;

        $stmt = $ligacao->prepare($sql);
        $stmt->execute($parametros);
        $localizacoes = $stmt->fetchAll();
    } catch (PDOException $err) {
        $erro_sistema = 'Erro ao carregar dados: ' . $err->getMessage();
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

    <main class="container my-5">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-1">Mapeamento Hospitalar</h1>
            <p class="text-muted mb-0">Gestão de edifícios, pisos e serviços clínicos</p>
        </div>

        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-body p-3">
                <form action="localizacoes.php" method="get">
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="search"
                                   name="pesquisa"
                                   class="form-control bg-light border-start-0"
                                   placeholder="Serviço, edifício, piso ou sala..."
                                   value="<?= h($filtros['pesquisa']) ?>">
                        </div>
                        <button class="btn btn-outline-secondary fw-bold"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#painelFiltrosLocalizacao">
                            <i class="fas fa-filter me-2"></i>Filtros
                        </button>
                        <button class="btn btn-primary fw-bold" type="submit">
                            <i class="fas fa-search me-2"></i>Pesquisar
                        </button>
                        <button class="btn btn-primary fw-bold"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modalNovaLocalizacao">
                            <i class="fas fa-map-marker-alt me-2"></i>Nova Localização
                        </button>
                    </div>

                    <div id="painelFiltrosLocalizacao"
                         class="collapse <?= $filtros['edificio'] !== '' || $filtros['piso'] !== '' || $filtros['estado'] !== '' || $filtros['ordenacao'] !== 'servico_asc' ? 'show' : '' ?>">
                        <div class="row g-3 pt-3">
                            <div class="col-md-6 col-lg-3">
                                <label for="filtro_edificio" class="form-label fw-bold">Edifício</label>
                                <select id="filtro_edificio" name="edificio" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($edificios as $edificio): ?>
                                        <option value="<?= h($edificio) ?>" <?= $filtros['edificio'] === $edificio ? 'selected' : '' ?>>
                                            <?= h($edificio) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="filtro_piso" class="form-label fw-bold">Piso</label>
                                <select id="filtro_piso" name="piso" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($pisos as $piso): ?>
                                        <option value="<?= h($piso) ?>" <?= $filtros['piso'] === $piso ? 'selected' : '' ?>>
                                            <?= h($piso) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="filtro_estado" class="form-label fw-bold">Estado</label>
                                <select id="filtro_estado" name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="ativo" <?= $filtros['estado'] === 'ativo' ? 'selected' : '' ?>>Ativas</option>
                                    <option value="inativo" <?= $filtros['estado'] === 'inativo' ? 'selected' : '' ?>>Inativas</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="ordenacao" class="form-label fw-bold">Ordenar por</label>
                                <select id="ordenacao" name="ordenacao" class="form-select">
                                    <option value="servico_asc" <?= $filtros['ordenacao'] === 'servico_asc' ? 'selected' : '' ?>>Serviço A-Z</option>
                                    <option value="servico_desc" <?= $filtros['ordenacao'] === 'servico_desc' ? 'selected' : '' ?>>Serviço Z-A</option>
                                    <option value="edificio_asc" <?= $filtros['ordenacao'] === 'edificio_asc' ? 'selected' : '' ?>>Edifício e piso</option>
                                    <option value="piso_asc" <?= $filtros['ordenacao'] === 'piso_asc' ? 'selected' : '' ?>>Piso</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <?= count($localizacoes) ?> resultado<?= count($localizacoes) === 1 ? '' : 's' ?>
                                </span>
                                <a href="localizacoes.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-eraser me-2"></i>Limpar filtros
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($localizacoes as $localizacao): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 feature-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                                    <i class="fas fa-map-marker-alt fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark"><?= h($localizacao['servico']) ?></h5>
                                    <span class="badge <?= $localizacao['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $localizacao['ativo'] ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Edifício:</span>
                                <span class="text-dark fw-medium ms-1"><?= h($localizacao['edificio']) ?></span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Piso:</span>
                                <span class="text-dark fw-medium ms-1"><?= h($localizacao['piso']) ?></span>
                            </div>
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">Sala:</span>
                                <span class="text-dark fw-medium ms-1"><?= h($localizacao['sala'] ?: 'Sem sala definida') ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top py-3 text-end">
                            <a href="localizacao_editar.php?id=<?= h(aes_encrypt($localizacao['id_localizacao'])) ?>"
                               class="btn btn-sm btn-outline-secondary fw-bold me-1">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <?php if ($localizacao['ativo'] && utilizador_administrador()): ?>
                                <a href="localizacao_confirmar_arquivo.php?id=<?= h(aes_encrypt($localizacao['id_localizacao'])) ?>"
                                   class="btn btn-sm btn-outline-danger fw-bold">
                                        <i class="fas fa-trash"></i> Arquivar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($localizacoes)): ?>
                <div class="col-12">
                    <div class="alert alert-info mb-0">Ainda não existem localizações registadas.</div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div class="modal fade" id="modalNovaLocalizacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Registar Localização</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Foram encontrados os seguintes erros:</strong>
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                    <li><?= h($erro) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($erro_sistema !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Erro:</strong>
                            <p class="mb-0"><?= h($erro_sistema) ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="localizacoes.php" method="post" novalidate>
                        <?= campo_csrf() ?>
                        <div class="mb-3">
                            <label for="servico" class="form-label fw-bold text-dark">Nome do Serviço / Departamento</label>
                            <input type="text" id="servico" name="servico" class="form-control" placeholder="Ex: Ortopedia" value="<?= h($valores['servico']) ?>" required>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="edificio" class="form-label fw-bold text-dark">Edifício</label>
                                <input type="text" id="edificio" name="edificio" class="form-control" placeholder="Ex: Edifício Principal" value="<?= h($valores['edificio']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="piso" class="form-label fw-bold text-dark">Piso</label>
                                <input type="text" id="piso" name="piso" class="form-control" placeholder="Ex: Piso 3" value="<?= h($valores['piso']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="sala" class="form-label fw-bold text-dark">Sala/Gabinete</label>
                                <input type="text" id="sala" name="sala" class="form-control" placeholder="Ex: ORT-01" value="<?= h($valores['sala']) ?>">
                            </div>
                        </div>
                        <div class="modal-footer bg-light mx-n4 mb-n4 px-4 py-3 border-top-0">
                            <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary fw-bold">Guardar Registo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($erros) || $erro_sistema !== ''): ?>
            new bootstrap.Modal(document.getElementById('modalNovaLocalizacao')).show();
            <?php endif; ?>
        });
    </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
