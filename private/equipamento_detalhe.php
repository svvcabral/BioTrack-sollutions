<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_autenticado();

$pageTitle = 'Ficha do Equipamento';
$activePage = 'equipamentos';
$erros = [];

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
        'manual_utilizador' => 'Manual de utilizador',
        'manual_servico' => 'Manual de serviço',
        'certificado_calibracao' => 'Certificado de calibração',
        'contrato_manutencao' => 'Contrato de manutenção',
        'declaracao_conformidade' => 'Declaração de conformidade',
        'relatorio_tecnico' => 'Relatório técnico',
    ];

    return $textos[$valor] ?? ucfirst(str_replace('_', ' ', $valor));
}

function data_valida(string $data): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return false;
    }

    [$ano, $mes, $dia] = array_map('intval', explode('-', $data));

    return checkdate($mes, $dia, $ano);
}

function formatar_data(?string $data): string
{
    if (!$data) {
        return 'Não indicada';
    }

    $objeto = DateTime::createFromFormat('Y-m-d', $data);

    return $objeto ? $objeto->format('d/m/Y') : $data;
}

function formatar_preco($valor): string
{
    if ($valor === null || $valor === '') {
        return 'Não indicado';
    }

    return number_format((float) $valor, 2, ',', '.') . ' €';
}

$id_encriptado = trim($_GET['id'] ?? '');
$id_equipamento = aes_decrypt($id_encriptado);
$aba_ativa = $_GET['aba'] ?? 'tecnicos';

if ($id_equipamento === false || !ctype_digit((string) $id_equipamento)) {
    header('Location: equipamentos.php?erro=equipamento_invalido');
    exit;
}

try {
    $ligacao = ligar_bd();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? '';
        $aba_ativa = 'documentacao';

        if ($acao === 'associar_fornecedor') {
            $id_fornecedor = trim($_POST['id_fornecedor'] ?? '');
            $funcao = trim($_POST['funcao'] ?? '');
            $data_inicio = trim($_POST['data_inicio'] ?? '');
            $data_fim = trim($_POST['data_fim'] ?? '');
            $funcoes = ['fabricante', 'distribuidor', 'assistencia_tecnica', 'consumiveis'];

            if ($id_fornecedor === '' || !ctype_digit($id_fornecedor)) {
                $erros[] = 'O fornecedor é obrigatório.';
            }
            if (!in_array($funcao, $funcoes, true)) {
                $erros[] = 'A função do fornecedor não é válida.';
            }
            if ($data_inicio !== '' && !data_valida($data_inicio)) {
                $erros[] = 'A data de início da associação não é válida.';
            }
            if ($data_fim !== '' && !data_valida($data_fim)) {
                $erros[] = 'A data de fim da associação não é válida.';
            }
            if ($data_inicio !== '' && $data_fim !== '' && $data_fim < $data_inicio) {
                $erros[] = 'A data de fim não pode ser anterior à data de início.';
            }

            if (empty($erros)) {
                $stmt = $ligacao->prepare(
                    'INSERT INTO equipamentos_fornecedores (
                        id_equipamento, id_fornecedor, funcao, data_inicio, data_fim
                     ) VALUES (
                        :id_equipamento, :id_fornecedor, :funcao, :data_inicio, :data_fim
                     )'
                );
                $stmt->execute([
                    ':id_equipamento' => (int) $id_equipamento,
                    ':id_fornecedor' => (int) $id_fornecedor,
                    ':funcao' => $funcao,
                    ':data_inicio' => $data_inicio ?: null,
                    ':data_fim' => $data_fim ?: null,
                ]);
                header('Location: equipamento_detalhe.php?id=' . urlencode($id_encriptado) . '&aba=documentacao&sucesso=fornecedor');
                exit;
            }
        }

        if ($acao === 'remover_fornecedor') {
            $id_fornecedor = trim($_POST['id_fornecedor'] ?? '');
            $funcao = trim($_POST['funcao'] ?? '');
            if (ctype_digit($id_fornecedor) && $funcao !== '') {
                $stmt = $ligacao->prepare(
                    'DELETE FROM equipamentos_fornecedores
                     WHERE id_equipamento = :id_equipamento
                       AND id_fornecedor = :id_fornecedor
                       AND funcao = :funcao'
                );
                $stmt->execute([
                    ':id_equipamento' => (int) $id_equipamento,
                    ':id_fornecedor' => (int) $id_fornecedor,
                    ':funcao' => $funcao,
                ]);
            }
            header('Location: equipamento_detalhe.php?id=' . urlencode($id_encriptado) . '&aba=documentacao');
            exit;
        }

        if ($acao === 'documento') {
            $tipo = trim($_POST['tipo'] ?? '');
            $nome = trim($_POST['nome'] ?? '');
            $data_documento = trim($_POST['data_documento'] ?? '');
            $data_validade = trim($_POST['data_validade'] ?? '');
            $caminho_ficheiro = trim($_POST['caminho_ficheiro'] ?? '');
            $id_fornecedor = trim($_POST['id_fornecedor'] ?? '');
            $tipos_documento = [
                'manual_utilizador',
                'manual_servico',
                'certificado_calibracao',
                'contrato_manutencao',
                'fatura',
                'declaracao_conformidade',
                'relatorio_tecnico',
                'outro',
            ];

            if (!in_array($tipo, $tipos_documento, true)) {
                $erros[] = 'O tipo de documento não é válido.';
            }
            if ($nome === '') {
                $erros[] = 'O nome do documento é obrigatório.';
            }
            if ($caminho_ficheiro === '') {
                $erros[] = 'O nome, ligação ou caminho do ficheiro é obrigatório.';
            }
            if ($data_documento !== '' && !data_valida($data_documento)) {
                $erros[] = 'A data do documento não é válida.';
            }
            if ($data_validade !== '' && !data_valida($data_validade)) {
                $erros[] = 'A data de validade não é válida.';
            }
            if ($data_documento !== '' && $data_validade !== '' && $data_validade < $data_documento) {
                $erros[] = 'A validade não pode ser anterior à data do documento.';
            }
            if ($id_fornecedor !== '' && !ctype_digit($id_fornecedor)) {
                $erros[] = 'O fornecedor selecionado não é válido.';
            }

            if (empty($erros)) {
                $stmt = $ligacao->prepare(
                    'INSERT INTO documentos (
                        tipo, nome, data_documento, data_validade, caminho_ficheiro,
                        id_equipamento, id_fornecedor
                     ) VALUES (
                        :tipo, :nome, :data_documento, :data_validade, :caminho_ficheiro,
                        :id_equipamento, :id_fornecedor
                     )'
                );
                $stmt->execute([
                    ':tipo' => $tipo,
                    ':nome' => $nome,
                    ':data_documento' => $data_documento ?: null,
                    ':data_validade' => $data_validade ?: null,
                    ':caminho_ficheiro' => $caminho_ficheiro,
                    ':id_equipamento' => (int) $id_equipamento,
                    ':id_fornecedor' => $id_fornecedor !== '' ? (int) $id_fornecedor : null,
                ]);

                header('Location: equipamento_detalhe.php?id=' . urlencode($id_encriptado) . '&aba=documentacao&sucesso=documento');
                exit;
            }
        }

        if ($acao === 'garantia') {
            $data_inicio = trim($_POST['data_inicio'] ?? '');
            $data_fim = trim($_POST['data_fim'] ?? '');
            $entidade_responsavel = trim($_POST['entidade_responsavel'] ?? '');
            $observacoes = trim($_POST['observacoes'] ?? '');

            if (!data_valida($data_inicio)) {
                $erros[] = 'A data de início da garantia é obrigatória e deve ser válida.';
            }
            if (!data_valida($data_fim)) {
                $erros[] = 'A data de fim da garantia é obrigatória e deve ser válida.';
            }
            if (data_valida($data_inicio) && data_valida($data_fim) && $data_fim < $data_inicio) {
                $erros[] = 'A data de fim da garantia não pode ser anterior ao início.';
            }

            if (empty($erros)) {
                $stmt = $ligacao->prepare(
                    'INSERT INTO garantias (
                        id_equipamento, data_inicio, data_fim,
                        entidade_responsavel, observacoes
                     ) VALUES (
                        :id_equipamento, :data_inicio, :data_fim,
                        :entidade_responsavel, :observacoes
                     )'
                );
                $stmt->execute([
                    ':id_equipamento' => (int) $id_equipamento,
                    ':data_inicio' => $data_inicio,
                    ':data_fim' => $data_fim,
                    ':entidade_responsavel' => $entidade_responsavel ?: null,
                    ':observacoes' => $observacoes ?: null,
                ]);

                header('Location: equipamento_detalhe.php?id=' . urlencode($id_encriptado) . '&aba=documentacao&sucesso=garantia');
                exit;
            }
        }

        if ($acao === 'contrato') {
            $id_fornecedor = trim($_POST['id_fornecedor'] ?? '');
            $tipo_contrato = trim($_POST['tipo_contrato'] ?? '');
            $data_inicio = trim($_POST['data_inicio'] ?? '');
            $data_fim = trim($_POST['data_fim'] ?? '');
            $periodicidade = trim($_POST['periodicidade'] ?? '');
            $observacoes = trim($_POST['observacoes'] ?? '');

            if ($id_fornecedor === '' || !ctype_digit($id_fornecedor)) {
                $erros[] = 'O fornecedor do contrato é obrigatório.';
            }
            if ($tipo_contrato === '') {
                $erros[] = 'O tipo de contrato é obrigatório.';
            }
            if (!data_valida($data_inicio)) {
                $erros[] = 'A data de início do contrato é obrigatória e deve ser válida.';
            }
            if ($data_fim !== '' && !data_valida($data_fim)) {
                $erros[] = 'A data de fim do contrato não é válida.';
            }
            if ($data_fim !== '' && data_valida($data_inicio) && data_valida($data_fim) && $data_fim < $data_inicio) {
                $erros[] = 'A data de fim do contrato não pode ser anterior ao início.';
            }

            if (empty($erros)) {
                $stmt = $ligacao->prepare(
                    'INSERT INTO contratos_manutencao (
                        id_equipamento, id_fornecedor, tipo_contrato,
                        data_inicio, data_fim, periodicidade, observacoes
                     ) VALUES (
                        :id_equipamento, :id_fornecedor, :tipo_contrato,
                        :data_inicio, :data_fim, :periodicidade, :observacoes
                     )'
                );
                $stmt->execute([
                    ':id_equipamento' => (int) $id_equipamento,
                    ':id_fornecedor' => (int) $id_fornecedor,
                    ':tipo_contrato' => $tipo_contrato,
                    ':data_inicio' => $data_inicio,
                    ':data_fim' => $data_fim ?: null,
                    ':periodicidade' => $periodicidade ?: null,
                    ':observacoes' => $observacoes ?: null,
                ]);

                header('Location: equipamento_detalhe.php?id=' . urlencode($id_encriptado) . '&aba=documentacao&sucesso=contrato');
                exit;
            }
        }
    }

    $stmt = $ligacao->prepare(
        'SELECT e.*, c.nome AS categoria, l.edificio, l.piso, l.servico, l.sala
         FROM equipamentos e
         INNER JOIN categorias c ON c.id_categoria = e.id_categoria
         INNER JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
         WHERE e.id_equipamento = :id_equipamento'
    );
    $stmt->execute([':id_equipamento' => (int) $id_equipamento]);
    $equipamento = $stmt->fetch();

    if (!$equipamento) {
        header('Location: equipamentos.php?erro=nao_encontrado');
        exit;
    }

    $fornecedores = $ligacao->query(
        'SELECT id_fornecedor, nome FROM fornecedores WHERE ativo = 1 ORDER BY nome'
    )->fetchAll();

    $stmt = $ligacao->prepare(
        'SELECT ef.*, f.nome AS fornecedor
         FROM equipamentos_fornecedores ef
         INNER JOIN fornecedores f ON f.id_fornecedor = ef.id_fornecedor
         WHERE ef.id_equipamento = :id_equipamento
         ORDER BY ef.funcao, f.nome'
    );
    $stmt->execute([':id_equipamento' => (int) $id_equipamento]);
    $fornecedores_associados = $stmt->fetchAll();

    $stmt = $ligacao->prepare(
        'SELECT d.*, f.nome AS fornecedor
         FROM documentos d
         LEFT JOIN fornecedores f ON f.id_fornecedor = d.id_fornecedor
         WHERE d.id_equipamento = :id_equipamento
         ORDER BY d.criado_em DESC'
    );
    $stmt->execute([':id_equipamento' => (int) $id_equipamento]);
    $documentos = $stmt->fetchAll();

    $stmt = $ligacao->prepare(
        'SELECT * FROM garantias
         WHERE id_equipamento = :id_equipamento
         ORDER BY data_fim DESC'
    );
    $stmt->execute([':id_equipamento' => (int) $id_equipamento]);
    $garantias = $stmt->fetchAll();

    $stmt = $ligacao->prepare(
        'SELECT cm.*, f.nome AS fornecedor
         FROM contratos_manutencao cm
         INNER JOIN fornecedores f ON f.id_fornecedor = cm.id_fornecedor
         WHERE cm.id_equipamento = :id_equipamento
         ORDER BY cm.ativo DESC, cm.data_fim DESC'
    );
    $stmt->execute([':id_equipamento' => (int) $id_equipamento]);
    $contratos = $stmt->fetchAll();
} catch (PDOException $erro) {
    $erros[] = 'Não foi possível concluir a operação: ' . $erro->getMessage();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container my-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <a href="equipamentos.php" class="text-decoration-none text-muted d-inline-block mb-2">
                <i class="fas fa-arrow-left me-1"></i>Voltar aos equipamentos
            </a>
            <h1 class="h2 fw-bold mb-1"><?= h($equipamento['designacao']) ?></h1>
            <p class="text-muted mb-0">
                Código de inventário:
                <span class="fw-bold text-dark"><?= h($equipamento['codigo_interno']) ?></span>
                <span class="badge ms-2 <?= $equipamento['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $equipamento['ativo'] ? 'Ativo' : 'Arquivado' ?>
                </span>
            </p>
        </div>
        <?php if ($equipamento['ativo']): ?>
            <a href="equipamento_editar.php?id=<?= h($id_encriptado) ?>"
               class="btn btn-outline-primary fw-bold">
                <i class="fas fa-edit me-2"></i>Editar equipamento
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">
            <strong>Foram encontrados os seguintes erros:</strong>
            <ul class="mb-0">
                <?php foreach ($erros as $erro): ?>
                    <li><?= h($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">O registo foi guardado com sucesso.</div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" id="abasEquipamento" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $aba_ativa !== 'documentacao' ? 'active' : '' ?>"
                    data-bs-toggle="tab" data-bs-target="#dados-tecnicos" type="button">
                <i class="fas fa-list me-2"></i>Dados Técnicos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $aba_ativa === 'documentacao' ? 'active' : '' ?>"
                    data-bs-toggle="tab" data-bs-target="#documentacao-garantias" type="button">
                <i class="fas fa-file-contract me-2"></i>Documentação e Garantias
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <section class="tab-pane fade <?= $aba_ativa !== 'documentacao' ? 'show active' : '' ?>"
                 id="dados-tecnicos">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 fw-bold mb-0">
                        <i class="fas fa-microchip text-primary me-2"></i>Identificação técnica
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <?php
                        $dados_tecnicos = [
                            'Código interno' => $equipamento['codigo_interno'],
                            'Designação' => $equipamento['designacao'],
                            'Categoria / grupo' => $equipamento['categoria'],
                            'Marca' => $equipamento['marca'],
                            'Modelo' => $equipamento['modelo'],
                            'Número de série' => $equipamento['numero_serie'],
                            'Fabricante' => $equipamento['fabricante'],
                        ];
                        ?>
                        <?php foreach ($dados_tecnicos as $rotulo => $valor): ?>
                            <div class="col-md-6 col-lg-4">
                                <p class="text-muted small fw-bold text-uppercase mb-1"><?= h($rotulo) ?></p>
                                <p class="mb-0"><?= h((string) $valor) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 fw-bold mb-0">
                        <i class="fas fa-shopping-cart text-primary me-2"></i>Aquisição, estado e localização
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Data de aquisição</p>
                            <p class="mb-0"><?= h(formatar_data($equipamento['data_aquisicao'])) ?></p>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Ano de fabrico</p>
                            <p class="mb-0"><?= h((string) ($equipamento['ano_fabrico'] ?: 'Não indicado')) ?></p>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Custo de aquisição</p>
                            <p class="mb-0"><?= h(formatar_preco($equipamento['custo_aquisicao'])) ?></p>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Tipo de entrada</p>
                            <p class="mb-0"><?= h(texto_opcao($equipamento['tipo_entrada'])) ?></p>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Estado atual</p>
                            <p class="mb-0"><?= h(texto_opcao($equipamento['estado'])) ?></p>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Criticidade</p>
                            <p class="mb-0"><?= h(texto_opcao($equipamento['criticidade'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Localização</p>
                            <p class="mb-0">
                                <?= h($equipamento['servico'] . ' - ' . $equipamento['edificio'] . ', ' . $equipamento['piso'] . ($equipamento['sala'] ? ', sala ' . $equipamento['sala'] : '')) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-bold text-uppercase mb-1">Observações</p>
                            <p class="mb-0"><?= nl2br(h((string) ($equipamento['observacoes'] ?: 'Sem observações.'))) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab-pane fade <?= $aba_ativa === 'documentacao' ? 'show active' : '' ?>"
                 id="documentacao-garantias">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Fornecedores associados</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFornecedor">
                    <i class="fas fa-plus me-2"></i>Associar fornecedor
                </button>
            </div>

            <div class="table-responsive bg-white shadow-sm mb-5">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fornecedor</th>
                            <th>Função</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fornecedores_associados as $associacao): ?>
                            <tr>
                                <td><?= h($associacao['fornecedor']) ?></td>
                                <td><?= h(texto_opcao($associacao['funcao'])) ?></td>
                                <td><?= h(formatar_data($associacao['data_inicio'])) ?></td>
                                <td><?= h(formatar_data($associacao['data_fim'])) ?></td>
                                <td class="text-end">
                                    <form method="post" action="equipamento_detalhe.php?id=<?= h($id_encriptado) ?>&aba=documentacao"
                                          onsubmit="return confirm('Remover esta associação?');">
                                        <input type="hidden" name="acao" value="remover_fornecedor">
                                        <input type="hidden" name="id_equipamento" value="<?= h((string) $id_equipamento) ?>">
                                        <input type="hidden" name="id_fornecedor" value="<?= h((string) $associacao['id_fornecedor']) ?>">
                                        <input type="hidden" name="funcao" value="<?= h($associacao['funcao']) ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-unlink"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($fornecedores_associados)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Não existem fornecedores associados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Garantias</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGarantia">
                    <i class="fas fa-plus me-2"></i>Registar garantia
                </button>
            </div>

            <div class="table-responsive bg-white shadow-sm mb-5">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Entidade responsável</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($garantias as $garantia): ?>
                            <tr>
                                <td><?= h(formatar_data($garantia['data_inicio'])) ?></td>
                                <td><?= h(formatar_data($garantia['data_fim'])) ?></td>
                                <td><?= h((string) ($garantia['entidade_responsavel'] ?: 'Não indicada')) ?></td>
                                <td><?= h((string) ($garantia['observacoes'] ?: 'Sem observações')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($garantias)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Não existem garantias registadas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Contratos de manutenção</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalContrato">
                    <i class="fas fa-plus me-2"></i>Registar contrato
                </button>
            </div>

            <div class="table-responsive bg-white shadow-sm mb-5">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Fornecedor</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Periodicidade</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contratos as $contrato): ?>
                            <tr>
                                <td><?= h($contrato['tipo_contrato']) ?></td>
                                <td><?= h($contrato['fornecedor']) ?></td>
                                <td><?= h(formatar_data($contrato['data_inicio'])) ?></td>
                                <td><?= h(formatar_data($contrato['data_fim'])) ?></td>
                                <td><?= h((string) ($contrato['periodicidade'] ?: 'Não indicada')) ?></td>
                                <td>
                                    <span class="badge <?= $contrato['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $contrato['ativo'] ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($contratos)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Não existem contratos registados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Documentação associada</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDocumento">
                    <i class="fas fa-plus me-2"></i>Anexar documento
                </button>
            </div>

            <div class="table-responsive bg-white shadow-sm">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Validade</th>
                            <th>Fornecedor</th>
                            <th class="text-end">Ficheiro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $documento): ?>
                            <tr>
                                <td><?= h(texto_opcao($documento['tipo'])) ?></td>
                                <td><?= h($documento['nome']) ?></td>
                                <td><?= h(formatar_data($documento['data_documento'])) ?></td>
                                <td><?= h(formatar_data($documento['data_validade'])) ?></td>
                                <td><?= h((string) ($documento['fornecedor'] ?: 'Não associado')) ?></td>
                                <td class="text-end">
                                    <a href="<?= h($documento['caminho_ficheiro']) ?>"
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($documentos)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Não existem documentos associados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<div class="modal fade" id="modalFornecedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="equipamento_detalhe.php?id=<?= h($id_encriptado) ?>&aba=documentacao" method="post">
            <div class="modal-header">
                <h2 class="modal-title h5">Associar fornecedor</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="acao" value="associar_fornecedor">
                <input type="hidden" name="id_equipamento" value="<?= h((string) $id_equipamento) ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">Fornecedor</label>
                    <select name="id_fornecedor" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($fornecedores as $fornecedor): ?>
                            <option value="<?= h((string) $fornecedor['id_fornecedor']) ?>"><?= h($fornecedor['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Função</label>
                    <select name="funcao" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="fabricante">Fabricante</option>
                        <option value="distribuidor">Distribuidor / fornecedor comercial</option>
                        <option value="assistencia_tecnica">Assistência técnica</option>
                        <option value="consumiveis">Consumíveis / acessórios</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de início</label>
                        <input type="text" name="data_inicio" class="form-control campo-data">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de fim</label>
                        <input type="text" name="data_fim" class="form-control campo-data">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar associação</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalGarantia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="equipamento_detalhe.php?id=<?= h($id_encriptado) ?>&aba=documentacao" method="post">
            <div class="modal-header">
                <h2 class="modal-title h5">Registar garantia</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="acao" value="garantia">
                <input type="hidden" name="id_equipamento" value="<?= h((string) $id_equipamento) ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">Data de início</label>
                    <input type="text" name="data_inicio" class="form-control campo-data" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Data de fim</label>
                    <input type="text" name="data_fim" class="form-control campo-data" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Entidade responsável</label>
                    <input type="text" name="entidade_responsavel" class="form-control">
                </div>
                <div>
                    <label class="form-label fw-bold">Observações</label>
                    <textarea name="observacoes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar garantia</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalContrato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="equipamento_detalhe.php?id=<?= h($id_encriptado) ?>&aba=documentacao" method="post">
            <div class="modal-header">
                <h2 class="modal-title h5">Registar contrato de manutenção</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="acao" value="contrato">
                <input type="hidden" name="id_equipamento" value="<?= h((string) $id_equipamento) ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fornecedor</label>
                        <select name="id_fornecedor" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?= h((string) $fornecedor['id_fornecedor']) ?>"><?= h($fornecedor['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de contrato</label>
                        <input type="text" name="tipo_contrato" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data de início</label>
                        <input type="text" name="data_inicio" class="form-control campo-data" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data de fim</label>
                        <input type="text" name="data_fim" class="form-control campo-data">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Periodicidade</label>
                        <input type="text" name="periodicidade" class="form-control" placeholder="Ex.: anual">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar contrato</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="equipamento_detalhe.php?id=<?= h($id_encriptado) ?>&aba=documentacao" method="post">
            <div class="modal-header">
                <h2 class="modal-title h5">Associar documento</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="acao" value="documento">
                <input type="hidden" name="id_equipamento" value="<?= h((string) $id_equipamento) ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de documento</label>
                        <select name="tipo" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="manual_utilizador">Manual de utilizador</option>
                            <option value="manual_servico">Manual de serviço</option>
                            <option value="certificado_calibracao">Certificado de calibração</option>
                            <option value="contrato_manutencao">Contrato de manutenção</option>
                            <option value="fatura">Fatura</option>
                            <option value="declaracao_conformidade">Declaração de conformidade</option>
                            <option value="relatorio_tecnico">Relatório técnico</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome do documento</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data do documento</label>
                        <input type="text" name="data_documento" class="form-control campo-data">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Data de validade</label>
                        <input type="text" name="data_validade" class="form-control campo-data">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fornecedor associado</label>
                        <select name="id_fornecedor" class="form-select">
                            <option value="">Sem fornecedor</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?= h((string) $fornecedor['id_fornecedor']) ?>"><?= h($fornecedor['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Nome, ligação ou caminho do ficheiro</label>
                        <input type="text" name="caminho_ficheiro" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar documento</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('.campo-data', {
        dateFormat: 'Y-m-d'
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
