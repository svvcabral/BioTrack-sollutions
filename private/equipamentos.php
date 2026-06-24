<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/validacoes.php';

redirecionar_se_nao_autenticado();
validar_csrf_post();

$pageTitle = 'Equipamentos';
$activePage = 'equipamentos';
$erros = [];
$erro_sistema = '';
$valores = [
    'codigo_interno' => '',
    'designacao' => '',
    'id_categoria' => '',
    'marca' => '',
    'modelo' => '',
    'numero_serie' => '',
    'fabricante' => '',
    'data_aquisicao' => '',
    'ano_fabrico' => '',
    'custo_aquisicao' => '',
    'tipo_entrada' => '',
    'estado' => '',
    'criticidade' => '',
    'id_localizacao' => '',
    'observacoes' => '',
];

$tipos_entrada = ['compra', 'doacao', 'aluguer', 'emprestimo'];
$estados = ['ativo', 'em_manutencao', 'inativo', 'em_calibracao', 'em_quarentena', 'abatido'];
$criticidades = ['baixa', 'media', 'alta', 'suporte_de_vida'];
$filtros = [
    'pesquisa' => trim($_GET['pesquisa'] ?? ''),
    'servico' => trim($_GET['servico'] ?? ''),
    'estado' => trim($_GET['estado'] ?? ''),
    'id_fornecedor' => trim($_GET['id_fornecedor'] ?? ''),
    'id_categoria' => trim($_GET['id_categoria'] ?? ''),
    'criticidade' => trim($_GET['criticidade'] ?? ''),
    'ordenacao' => trim($_GET['ordenacao'] ?? 'recentes'),
];

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function texto_opcao(string $valor): string
{
    $textos = [
        'doacao' => 'Doação',
        'emprestimo' => 'Empréstimo',
        'em_manutencao' => 'Em manutenção',
        'em_calibracao' => 'Em calibração',
        'em_quarentena' => 'Em quarentena',
        'suporte_de_vida' => 'Suporte de vida',
    ];

    return $textos[$valor] ?? ucfirst($valor);
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

    $erros = validar_equipamento($valores);

    if (empty($erros)) {
        $valores['codigo_interno'] = strtoupper($valores['codigo_interno']);
        $valores['designacao'] = ucwords(strtolower($valores['designacao']));
        $valores['marca'] = ucwords(strtolower($valores['marca']));
        $valores['modelo'] = ucwords(strtolower($valores['modelo']));
        $valores['numero_serie'] = strtoupper($valores['numero_serie']);
        $valores['fabricante'] = ucwords(strtolower($valores['fabricante']));
        $valores['custo_aquisicao'] = str_replace(',', '.', $valores['custo_aquisicao']);

        try {
            $sql = "INSERT INTO equipamentos (
                codigo_interno, designacao, id_categoria, marca, modelo, numero_serie,
                fabricante, data_aquisicao, ano_fabrico, custo_aquisicao, tipo_entrada,
                estado, criticidade, observacoes, id_localizacao
            ) VALUES (
                :codigo_interno, :designacao, :id_categoria, :marca, :modelo, :numero_serie,
                :fabricante, :data_aquisicao, :ano_fabrico, :custo_aquisicao, :tipo_entrada,
                :estado, :criticidade, :observacoes, :id_localizacao
            )";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':codigo_interno' => $valores['codigo_interno'],
                ':designacao' => $valores['designacao'],
                ':id_categoria' => (int) $valores['id_categoria'],
                ':marca' => $valores['marca'],
                ':modelo' => $valores['modelo'],
                ':numero_serie' => $valores['numero_serie'],
                ':fabricante' => $valores['fabricante'],
                ':data_aquisicao' => $valores['data_aquisicao'] !== '' ? $valores['data_aquisicao'] : null,
                ':ano_fabrico' => $valores['ano_fabrico'] !== '' ? (int) $valores['ano_fabrico'] : null,
                ':custo_aquisicao' => $valores['custo_aquisicao'] !== '' ? $valores['custo_aquisicao'] : null,
                ':tipo_entrada' => $valores['tipo_entrada'],
                ':estado' => $valores['estado'],
                ':criticidade' => $valores['criticidade'],
                ':observacoes' => $valores['observacoes'] !== '' ? $valores['observacoes'] : null,
                ':id_localizacao' => (int) $valores['id_localizacao'],
            ]);

            registar_log(
                $ligacao,
                'criar_equipamento',
                'equipamentos',
                (int) $ligacao->lastInsertId(),
                $valores['codigo_interno'] . ' - ' . $valores['designacao']
            );
            header('Location: equipamentos.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = 'Erro ao gravar os dados: ' . $err->getMessage();
        }
    }
}

$categorias = [];
$localizacoes = [];
$fornecedores = [];
$equipamentos = [];

if ($ligacao instanceof PDO) {
    try {
        $categorias = $ligacao->query('SELECT id_categoria, nome FROM categorias ORDER BY nome')->fetchAll();
        $localizacoes = $ligacao->query('SELECT id_localizacao, edificio, piso, servico, sala FROM localizacoes WHERE ativo = 1 ORDER BY servico')->fetchAll();
        $fornecedores = $ligacao->query(
            'SELECT id_fornecedor, nome FROM fornecedores WHERE ativo = 1 ORDER BY nome'
        )->fetchAll();

        $condicoes = ['e.ativo = 1'];
        $parametros = [];

        if ($filtros['pesquisa'] !== '') {
            $condicoes[] = '(e.codigo_interno LIKE :pesquisa_codigo
                             OR e.designacao LIKE :pesquisa_designacao
                             OR e.marca LIKE :pesquisa_marca
                             OR e.modelo LIKE :pesquisa_modelo
                             OR e.numero_serie LIKE :pesquisa_serie)';
            $termo = '%' . $filtros['pesquisa'] . '%';
            $parametros[':pesquisa_codigo'] = $termo;
            $parametros[':pesquisa_designacao'] = $termo;
            $parametros[':pesquisa_marca'] = $termo;
            $parametros[':pesquisa_modelo'] = $termo;
            $parametros[':pesquisa_serie'] = $termo;
        }

        if ($filtros['servico'] !== '') {
            $condicoes[] = 'l.servico = :servico';
            $parametros[':servico'] = $filtros['servico'];
        }

        if ($filtros['estado'] !== '' && in_array($filtros['estado'], $estados, true)) {
            $condicoes[] = 'e.estado = :estado';
            $parametros[':estado'] = $filtros['estado'];
        }

        if ($filtros['id_categoria'] !== '' && ctype_digit($filtros['id_categoria'])) {
            $condicoes[] = 'e.id_categoria = :id_categoria';
            $parametros[':id_categoria'] = (int) $filtros['id_categoria'];
        }

        if ($filtros['criticidade'] !== '' && in_array($filtros['criticidade'], $criticidades, true)) {
            $condicoes[] = 'e.criticidade = :criticidade';
            $parametros[':criticidade'] = $filtros['criticidade'];
        }

        if ($filtros['id_fornecedor'] !== '' && ctype_digit($filtros['id_fornecedor'])) {
            $condicoes[] = 'EXISTS (
                SELECT 1
                FROM equipamentos_fornecedores ef
                WHERE ef.id_equipamento = e.id_equipamento
                  AND ef.id_fornecedor = :id_fornecedor
            )';
            $parametros[':id_fornecedor'] = (int) $filtros['id_fornecedor'];
        }

        $ordenacoes = [
            'recentes' => 'e.criado_em DESC, e.designacao ASC',
            'codigo_asc' => 'e.codigo_interno ASC',
            'codigo_desc' => 'e.codigo_interno DESC',
            'designacao_asc' => 'e.designacao ASC',
            'designacao_desc' => 'e.designacao DESC',
            'marca_asc' => 'e.marca ASC, e.modelo ASC',
        ];
        $ordem = $ordenacoes[$filtros['ordenacao']] ?? $ordenacoes['recentes'];

        $sql = "SELECT e.id_equipamento,
                       e.codigo_interno,
                       e.designacao,
                       e.marca,
                       e.modelo,
                       e.criticidade,
                       l.servico AS localizacao
                FROM equipamentos e
                INNER JOIN localizacoes l
                    ON l.id_localizacao = e.id_localizacao
                WHERE " . implode(' AND ', $condicoes) . "
                ORDER BY " . $ordem;

        $stmt = $ligacao->prepare($sql);
        $stmt->execute($parametros);
        $equipamentos = $stmt->fetchAll();
    } catch (PDOException $err) {
        $erro_sistema = 'Erro ao carregar dados: ' . $err->getMessage();
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

    <main class="container my-5">
        <div class="mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                <div>
                    <h1 class="h2 fw-bold mb-1">Inventário de Equipamentos</h1>
                    <p class="text-muted mb-0">Gestão global de dispositivos médicos e rastreabilidade</p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-2"></i>Exportar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="exportar_equipamentos.php?formato=csv">CSV</a></li>
                        <li><a class="dropdown-item" href="exportar_equipamentos.php?formato=json">JSON</a></li>
                        <li><a class="dropdown-item" href="exportar_equipamentos.php?formato=pdf">PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white border-bottom p-3">
                <form action="equipamentos.php" method="get">
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="search"
                                   name="pesquisa"
                                   class="form-control bg-light border-start-0"
                                   placeholder="Código, designação, marca, modelo ou número de série..."
                                   value="<?= h($filtros['pesquisa']) ?>">
                        </div>
                        <button class="btn btn-outline-secondary fw-bold"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#painelFiltros"
                                aria-expanded="<?= array_filter(array_slice($filtros, 1, 5)) ? 'true' : 'false' ?>">
                            <i class="fas fa-filter me-2"></i>Filtros
                        </button>
                        <button class="btn btn-primary fw-bold" type="submit">
                            <i class="fas fa-search me-2"></i>Pesquisar
                        </button>
                        <button class="btn btn-primary fw-bold"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modalNovoEquipamento">
                            <i class="fas fa-plus me-2"></i>Registar Equipamento
                        </button>
                    </div>

                    <div id="painelFiltros"
                         class="collapse <?= array_filter(array_slice($filtros, 1, 5)) ? 'show' : '' ?>">
                        <div class="row g-3 pt-3">
                            <div class="col-md-6 col-lg-4">
                                <label for="filtro_servico" class="form-label fw-bold">Serviço</label>
                                <select id="filtro_servico" name="servico" class="form-select">
                                    <option value="">Todos</option>
                                    <?php
                                    $servicos_apresentados = [];
                                    foreach ($localizacoes as $localizacao):
                                        if (in_array($localizacao['servico'], $servicos_apresentados, true)) {
                                            continue;
                                        }
                                        $servicos_apresentados[] = $localizacao['servico'];
                                    ?>
                                        <option value="<?= h($localizacao['servico']) ?>"
                                            <?= $filtros['servico'] === $localizacao['servico'] ? 'selected' : '' ?>>
                                            <?= h($localizacao['servico']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="filtro_estado" class="form-label fw-bold">Estado</label>
                                <select id="filtro_estado" name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= h($estado) ?>"
                                            <?= $filtros['estado'] === $estado ? 'selected' : '' ?>>
                                            <?= h(texto_opcao($estado)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="filtro_fornecedor" class="form-label fw-bold">Fornecedor</label>
                                <select id="filtro_fornecedor" name="id_fornecedor" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($fornecedores as $fornecedor): ?>
                                        <option value="<?= h((string) $fornecedor['id_fornecedor']) ?>"
                                            <?= $filtros['id_fornecedor'] == $fornecedor['id_fornecedor'] ? 'selected' : '' ?>>
                                            <?= h($fornecedor['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="filtro_categoria" class="form-label fw-bold">Categoria</label>
                                <select id="filtro_categoria" name="id_categoria" class="form-select">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= h((string) $categoria['id_categoria']) ?>"
                                            <?= $filtros['id_categoria'] == $categoria['id_categoria'] ? 'selected' : '' ?>>
                                            <?= h($categoria['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="filtro_criticidade" class="form-label fw-bold">Criticidade</label>
                                <select id="filtro_criticidade" name="criticidade" class="form-select">
                                    <option value="">Todas</option>
                                    <?php foreach ($criticidades as $criticidade): ?>
                                        <option value="<?= h($criticidade) ?>"
                                            <?= $filtros['criticidade'] === $criticidade ? 'selected' : '' ?>>
                                            <?= h(texto_opcao($criticidade)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="ordenacao" class="form-label fw-bold">Ordenar por</label>
                                <select id="ordenacao" name="ordenacao" class="form-select">
                                    <option value="recentes" <?= $filtros['ordenacao'] === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
                                    <option value="codigo_asc" <?= $filtros['ordenacao'] === 'codigo_asc' ? 'selected' : '' ?>>Código A-Z</option>
                                    <option value="codigo_desc" <?= $filtros['ordenacao'] === 'codigo_desc' ? 'selected' : '' ?>>Código Z-A</option>
                                    <option value="designacao_asc" <?= $filtros['ordenacao'] === 'designacao_asc' ? 'selected' : '' ?>>Designação A-Z</option>
                                    <option value="designacao_desc" <?= $filtros['ordenacao'] === 'designacao_desc' ? 'selected' : '' ?>>Designação Z-A</option>
                                    <option value="marca_asc" <?= $filtros['ordenacao'] === 'marca_asc' ? 'selected' : '' ?>>Marca A-Z</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <?= count($equipamentos) ?> resultado<?= count($equipamentos) === 1 ? '' : 's' ?>
                                </span>
                                <a href="equipamentos.php" class="btn btn-outline-secondary">
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
                                <th class="px-4 py-3">Código</th>
                                <th class="py-3">Designação</th>
                                <th class="py-3">Marca / Modelo</th>
                                <th class="py-3">Localização</th>
                                <th class="py-3">Criticidade</th>
                                <th class="px-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equipamentos as $equipamento): ?>
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-primary"><?= h($equipamento['codigo_interno']) ?></td>
                                    <td class="py-3 fw-medium"><?= h($equipamento['designacao']) ?></td>
                                    <td class="py-3"><?= h($equipamento['marca'] . ' ' . $equipamento['modelo']) ?></td>
                                    <td class="py-3"><?= h($equipamento['localizacao']) ?></td>
<td class="py-3">
    <span class="badge bg-primary">
        <?= h(texto_opcao($equipamento['criticidade'])) ?>
    </span>
</td>

<td class="px-4 py-3 text-end">
    <div class="d-flex justify-content-end gap-1">

        <a href="equipamento_detalhe.php?id=<?= h(aes_encrypt($equipamento['id_equipamento'])) ?>"
           class="btn btn-sm btn-outline-primary"
           title="Ver detalhes">
            <i class="fas fa-eye"></i>
        </a>

        <a href="equipamento_editar.php?id=<?= h(aes_encrypt($equipamento['id_equipamento'])) ?>"
           class="btn btn-sm btn-outline-secondary"
           title="Editar equipamento">
            <i class="fas fa-edit"></i>
        </a>

        <?php if (utilizador_administrador()): ?>
            <a href="equipamento_confirmar_arquivo.php?id=<?= h(aes_encrypt($equipamento['id_equipamento'])) ?>"
               class="btn btn-sm btn-outline-danger"
               title="Arquivar equipamento">
                    <i class="fas fa-trash-alt"></i>
            </a>
        <?php endif; ?>

    </div>
</td>
                            </tr>

                            <?php endforeach; ?>
                            <?php if (empty($equipamentos)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Ainda não existem equipamentos registados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalNovoEquipamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Registar Novo Equipamento</h5>
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

                    <form action="equipamentos.php" method="post" novalidate>
                        <?= campo_csrf() ?>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="codigo_interno" class="form-label fw-bold text-dark">Código interno</label>
                                <input type="text" id="codigo_interno" name="codigo_interno" class="form-control" value="<?= h($valores['codigo_interno']) ?>" required>
                            </div>
                            <div class="col-md-8">
                                <label for="designacao" class="form-label fw-bold text-dark">Designação</label>
                                <input type="text" id="designacao" name="designacao" class="form-control" value="<?= h($valores['designacao']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="id_categoria" class="form-label fw-bold text-dark">Categoria</label>
                                <select id="id_categoria" name="id_categoria" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= h((string) $categoria['id_categoria']) ?>" <?= $valores['id_categoria'] == $categoria['id_categoria'] ? 'selected' : '' ?>><?= h($categoria['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="marca" class="form-label fw-bold text-dark">Marca</label>
                                <input type="text" id="marca" name="marca" class="form-control" value="<?= h($valores['marca']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="modelo" class="form-label fw-bold text-dark">Modelo</label>
                                <input type="text" id="modelo" name="modelo" class="form-control" value="<?= h($valores['modelo']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="numero_serie" class="form-label fw-bold text-dark">Número de série</label>
                                <input type="text" id="numero_serie" name="numero_serie" class="form-control" value="<?= h($valores['numero_serie']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fabricante" class="form-label fw-bold text-dark">Fabricante</label>
                                <input type="text" id="fabricante" name="fabricante" class="form-control" value="<?= h($valores['fabricante']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="data_aquisicao" class="form-label fw-bold text-dark">Data de aquisição</label>
                                <input type="text" id="data_aquisicao" name="data_aquisicao" class="form-control" value="<?= h($valores['data_aquisicao']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="ano_fabrico" class="form-label fw-bold text-dark">Ano de fabrico</label>
                                <input type="number" id="ano_fabrico" name="ano_fabrico" class="form-control" value="<?= h($valores['ano_fabrico']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="custo_aquisicao" class="form-label fw-bold text-dark">Custo de aquisição</label>
                                <input type="text" id="custo_aquisicao" name="custo_aquisicao" class="form-control" value="<?= h($valores['custo_aquisicao']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_entrada" class="form-label fw-bold text-dark">Tipo de entrada</label>
                                <select id="tipo_entrada" name="tipo_entrada" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($tipos_entrada as $tipo): ?>
                                        <option value="<?= h($tipo) ?>" <?= $valores['tipo_entrada'] === $tipo ? 'selected' : '' ?>><?= h(texto_opcao($tipo)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="form-label fw-bold text-dark">Estado atual</label>
                                <select id="estado" name="estado" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= h($estado) ?>" <?= $valores['estado'] === $estado ? 'selected' : '' ?>><?= h(texto_opcao($estado)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="criticidade" class="form-label fw-bold text-dark">Criticidade</label>
                                <select id="criticidade" name="criticidade" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($criticidades as $criticidade): ?>
                                        <option value="<?= h($criticidade) ?>" <?= $valores['criticidade'] === $criticidade ? 'selected' : '' ?>><?= h(texto_opcao($criticidade)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="id_localizacao" class="form-label fw-bold text-dark">Localização atual</label>
                                <select id="id_localizacao" name="id_localizacao" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($localizacoes as $localizacao): ?>
                                        <option value="<?= h((string) $localizacao['id_localizacao']) ?>" <?= $valores['id_localizacao'] == $localizacao['id_localizacao'] ? 'selected' : '' ?>>
                                            <?= h($localizacao['servico'] . ' - ' . $localizacao['edificio'] . ', ' . $localizacao['piso']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
            flatpickr("#data_aquisicao", {
                dateFormat: "Y-m-d",
                maxDate: "today"
            });
            <?php if (!empty($erros) || $erro_sistema !== ''): ?>
            new bootstrap.Modal(document.getElementById('modalNovoEquipamento')).show();
            <?php endif; ?>
        });
    </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
