<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/validacoes.php';

redirecionar_se_nao_administrador();
validar_csrf_post();

$pageTitle = 'Fornecedores';
$activePage = 'fornecedores';
$erros = [];
$erro_sistema = '';
$valores = [
    'nome' => '',
    'nif' => '',
    'telefone' => '',
    'email' => '',
    'morada' => '',
    'website' => '',
    'pessoa_contacto' => '',
    'telefone_contacto' => '',
    'tipo' => '',
    'observacoes' => '',
];
$tipos_fornecedor = ['fabricante', 'distribuidor', 'assistencia_tecnica', 'consumiveis'];
$filtros = [
    'pesquisa' => trim($_GET['pesquisa'] ?? ''),
    'tipo' => trim($_GET['tipo'] ?? ''),
    'estado' => trim($_GET['estado'] ?? ''),
    'ordenacao' => trim($_GET['ordenacao'] ?? 'nome_asc'),
];

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function texto_tipo_fornecedor(string $tipo): string
{
    $textos = [
        'assistencia_tecnica' => 'Assistência técnica',
        'consumiveis' => 'Consumíveis',
    ];

    return $textos[$tipo] ?? ucfirst($tipo);
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

    $erros = validar_fornecedor($valores);

    if (empty($erros)) {
        $valores['nome'] = ucwords(strtolower($valores['nome']));
        $valores['email'] = strtolower($valores['email']);
        $valores['pessoa_contacto'] = ucwords(strtolower($valores['pessoa_contacto']));

        try {
            $sql = "INSERT INTO fornecedores (
                nome, nif, telefone, email, morada, website, pessoa_contacto,
                telefone_contacto, tipo, observacoes
            ) VALUES (
                :nome, :nif, :telefone, :email, :morada, :website, :pessoa_contacto,
                :telefone_contacto, :tipo, :observacoes
            )";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':nome' => $valores['nome'],
                ':nif' => $valores['nif'],
                ':telefone' => $valores['telefone'] !== '' ? $valores['telefone'] : null,
                ':email' => $valores['email'],
                ':morada' => $valores['morada'] !== '' ? $valores['morada'] : null,
                ':website' => $valores['website'] !== '' ? $valores['website'] : null,
                ':pessoa_contacto' => $valores['pessoa_contacto'] !== '' ? $valores['pessoa_contacto'] : null,
                ':telefone_contacto' => $valores['telefone_contacto'] !== '' ? $valores['telefone_contacto'] : null,
                ':tipo' => $valores['tipo'],
                ':observacoes' => $valores['observacoes'] !== '' ? $valores['observacoes'] : null,
            ]);

            header('Location: fornecedores.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = 'Erro ao gravar os dados: ' . $err->getMessage();
        }
    }
}

$fornecedores = [];
if ($ligacao instanceof PDO) {
    try {
        $condicoes = [];
        $parametros = [];

        if ($filtros['pesquisa'] !== '') {
            $condicoes[] = '(nome LIKE :pesquisa_nome
                             OR nif LIKE :pesquisa_nif
                             OR email LIKE :pesquisa_email
                             OR telefone LIKE :pesquisa_telefone
                             OR pessoa_contacto LIKE :pesquisa_contacto)';
            $termo = '%' . $filtros['pesquisa'] . '%';
            $parametros[':pesquisa_nome'] = $termo;
            $parametros[':pesquisa_nif'] = $termo;
            $parametros[':pesquisa_email'] = $termo;
            $parametros[':pesquisa_telefone'] = $termo;
            $parametros[':pesquisa_contacto'] = $termo;
        }

        if ($filtros['tipo'] !== '' && in_array($filtros['tipo'], $tipos_fornecedor, true)) {
            $condicoes[] = 'tipo = :tipo';
            $parametros[':tipo'] = $filtros['tipo'];
        }

        if ($filtros['estado'] === 'ativo') {
            $condicoes[] = 'ativo = 1';
        } elseif ($filtros['estado'] === 'inativo') {
            $condicoes[] = 'ativo = 0';
        }

        $ordenacoes = [
            'nome_asc' => 'nome ASC',
            'nome_desc' => 'nome DESC',
            'nif_asc' => 'nif ASC',
            'tipo_asc' => 'tipo ASC, nome ASC',
            'recentes' => 'criado_em DESC',
        ];
        $ordem = $ordenacoes[$filtros['ordenacao']] ?? $ordenacoes['nome_asc'];

        $sql = 'SELECT id_fornecedor, nome, nif, tipo, email, telefone, ativo
                FROM fornecedores';
        if (!empty($condicoes)) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }
        $sql .= ' ORDER BY ' . $ordem;

        $stmt = $ligacao->prepare($sql);
        $stmt->execute($parametros);
        $fornecedores = $stmt->fetchAll();
    } catch (PDOException $err) {
        $erro_sistema = 'Erro ao carregar dados: ' . $err->getMessage();
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

    <main class="container my-5">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-1">Gestão de Fornecedores</h1>
            <p class="text-muted mb-0">Fabricantes e entidades de assistência técnica autorizadas</p>
        </div>

        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white border-bottom p-3">
                <form action="fornecedores.php" method="get">
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="search"
                                   name="pesquisa"
                                   class="form-control bg-light border-start-0"
                                   placeholder="Empresa, NIF, email, telefone ou contacto..."
                                   value="<?= h($filtros['pesquisa']) ?>">
                        </div>
                        <button class="btn btn-outline-secondary fw-bold"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#painelFiltrosFornecedor">
                            <i class="fas fa-filter me-2"></i>Filtros
                        </button>
                        <button class="btn btn-primary fw-bold" type="submit">
                            <i class="fas fa-search me-2"></i>Pesquisar
                        </button>
                        <button class="btn btn-primary fw-bold"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modalNovoFornecedor">
                            <i class="fas fa-handshake me-2"></i>Adicionar Fornecedor
                        </button>
                    </div>

                    <div id="painelFiltrosFornecedor"
                         class="collapse <?= $filtros['tipo'] !== '' || $filtros['estado'] !== '' || $filtros['ordenacao'] !== 'nome_asc' ? 'show' : '' ?>">
                        <div class="row g-3 pt-3">
                            <div class="col-md-4">
                                <label for="filtro_tipo" class="form-label fw-bold">Tipo</label>
                                <select id="filtro_tipo" name="tipo" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($tipos_fornecedor as $tipo): ?>
                                        <option value="<?= h($tipo) ?>" <?= $filtros['tipo'] === $tipo ? 'selected' : '' ?>>
                                            <?= h(texto_tipo_fornecedor($tipo)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filtro_estado" class="form-label fw-bold">Estado</label>
                                <select id="filtro_estado" name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="ativo" <?= $filtros['estado'] === 'ativo' ? 'selected' : '' ?>>Ativos</option>
                                    <option value="inativo" <?= $filtros['estado'] === 'inativo' ? 'selected' : '' ?>>Inativos</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ordenacao" class="form-label fw-bold">Ordenar por</label>
                                <select id="ordenacao" name="ordenacao" class="form-select">
                                    <option value="nome_asc" <?= $filtros['ordenacao'] === 'nome_asc' ? 'selected' : '' ?>>Empresa A-Z</option>
                                    <option value="nome_desc" <?= $filtros['ordenacao'] === 'nome_desc' ? 'selected' : '' ?>>Empresa Z-A</option>
                                    <option value="nif_asc" <?= $filtros['ordenacao'] === 'nif_asc' ? 'selected' : '' ?>>NIF crescente</option>
                                    <option value="tipo_asc" <?= $filtros['ordenacao'] === 'tipo_asc' ? 'selected' : '' ?>>Tipo</option>
                                    <option value="recentes" <?= $filtros['ordenacao'] === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <?= count($fornecedores) ?> resultado<?= count($fornecedores) === 1 ? '' : 's' ?>
                                </span>
                                <a href="fornecedores.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-eraser me-2"></i>Limpar filtros
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Empresa</th>
                                <th class="py-3">NIF</th>
                                <th class="py-3">Tipo</th>
                                <th class="py-3">Contacto Principal</th>
                                <th class="py-3">Estado</th>
                                <th class="px-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <tr>
                                    <td class="px-4 fw-bold text-secondary"><?= h($fornecedor['nome']) ?></td>
                                    <td><?= h($fornecedor['nif']) ?></td>
                                    <td><?= h(texto_tipo_fornecedor($fornecedor['tipo'])) ?></td>
                                    <td><?= h($fornecedor['email'] ?: $fornecedor['telefone'] ?: 'Sem contacto') ?></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success"><?= $fornecedor['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
                                    <td class="px-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="fornecedor_editar.php?id=<?= h(aes_encrypt($fornecedor['id_fornecedor'])) ?>"
                                               class="btn btn-sm btn-outline-secondary" title="Editar fornecedor">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($fornecedor['ativo']): ?>
                                                <a href="fornecedor_confirmar_arquivo.php?id=<?= h(aes_encrypt($fornecedor['id_fornecedor'])) ?>"
                                                   class="btn btn-sm btn-outline-danger" title="Arquivar fornecedor">
                                                        <i class="fas fa-trash-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($fornecedores)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Ainda não existem fornecedores registados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalNovoFornecedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Registar Fornecedor</h5>
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

                    <form action="fornecedores.php" method="post" novalidate>
                        <?= campo_csrf() ?>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nome" class="form-label fw-bold text-dark">Nome da empresa</label>
                                <input type="text" id="nome" name="nome" class="form-control" value="<?= h($valores['nome']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="nif" class="form-label fw-bold text-dark">NIF</label>
                                <input type="text" id="nif" name="nif" class="form-control" value="<?= h($valores['nif']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="telefone" class="form-label fw-bold text-dark">Telefone</label>
                                <input type="text" id="telefone" name="telefone" class="form-control" value="<?= h($valores['telefone']) ?>">
                            </div>
                            <div class="col-md-8">
                                <label for="email" class="form-label fw-bold text-dark">Email</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= h($valores['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label fw-bold text-dark">Website</label>
                                <input type="url" id="website" name="website" class="form-control" value="<?= h($valores['website']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="tipo" class="form-label fw-bold text-dark">Tipo de fornecedor</label>
                                <select id="tipo" name="tipo" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($tipos_fornecedor as $tipo): ?>
                                        <option value="<?= h($tipo) ?>" <?= $valores['tipo'] === $tipo ? 'selected' : '' ?>><?= h(texto_tipo_fornecedor($tipo)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="pessoa_contacto" class="form-label fw-bold text-dark">Pessoa de contacto</label>
                                <input type="text" id="pessoa_contacto" name="pessoa_contacto" class="form-control" value="<?= h($valores['pessoa_contacto']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="telefone_contacto" class="form-label fw-bold text-dark">Telefone da pessoa de contacto</label>
                                <input type="text" id="telefone_contacto" name="telefone_contacto" class="form-control" value="<?= h($valores['telefone_contacto']) ?>">
                            </div>
                            <div class="col-12">
                                <label for="morada" class="form-label fw-bold text-dark">Morada</label>
                                <input type="text" id="morada" name="morada" class="form-control" value="<?= h($valores['morada']) ?>">
                            </div>
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-bold text-dark">Observações</label>
                                <textarea id="observacoes" name="observacoes" class="form-control" rows="3"><?= h($valores['observacoes']) ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light mt-4 mx-n4 mb-n4 px-4 py-3 border-top-0">
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
            new bootstrap.Modal(document.getElementById('modalNovoFornecedor')).show();
            <?php endif; ?>
        });
    </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
