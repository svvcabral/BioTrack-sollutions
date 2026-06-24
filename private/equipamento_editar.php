<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/validacoes.php';

redirecionar_se_nao_autenticado();
permitir_apenas_get_post();

$pageTitle = 'Editar Equipamento';
$activePage = 'equipamentos';
$erros = [];

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$id_encriptado = trim($_GET['id'] ?? '');
$id_equipamento = aes_decrypt($id_encriptado);

if ($id_equipamento === false || !ctype_digit((string) $id_equipamento)) {
    header('Location: equipamentos.php?erro=equipamento_invalido');
    exit;
}

$tipos_entrada = ['compra', 'doacao', 'aluguer', 'emprestimo'];

$estados = [
    'ativo',
    'em_manutencao',
    'inativo',
    'em_calibracao',
    'em_quarentena',
    'abatido'
];

$criticidades = [
    'baixa',
    'media',
    'alta',
    'suporte_de_vida'
];

try {
    $ligacao = ligar_bd();

    $categorias = $ligacao
        ->query('SELECT id_categoria, nome FROM categorias ORDER BY nome')
        ->fetchAll();

    $localizacoes = $ligacao
        ->query(
            'SELECT id_localizacao, edificio, piso, servico, sala
             FROM localizacoes
             WHERE ativo = 1
             ORDER BY servico'
        )
        ->fetchAll();

    $stmt = $ligacao->prepare(
        'SELECT *
         FROM equipamentos
         WHERE id_equipamento = :id_equipamento
         AND ativo = 1'
    );

    $stmt->execute([
        ':id_equipamento' => (int) $id_equipamento
    ]);

    $equipamento = $stmt->fetch();

    if (!$equipamento) {
        header('Location: equipamentos.php?erro=nao_encontrado');
        exit;
    }
} catch (PDOException $erro) {
    exit('Erro ao carregar o equipamento.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'codigo_interno',
        'designacao',
        'id_categoria',
        'marca',
        'modelo',
        'numero_serie',
        'fabricante',
        'data_aquisicao',
        'ano_fabrico',
        'custo_aquisicao',
        'tipo_entrada',
        'estado',
        'criticidade',
        'id_localizacao',
        'observacoes'
    ];

    foreach ($campos as $campo) {
        $equipamento[$campo] = trim($_POST[$campo] ?? '');
    }

    $erros = validar_equipamento($equipamento);
    $custo = str_replace(',', '.', $equipamento['custo_aquisicao']);

    if (empty($erros)) {
        try {
            $stmt = $ligacao->prepare(
                'UPDATE equipamentos SET
                    codigo_interno = :codigo_interno,
                    designacao = :designacao,
                    id_categoria = :id_categoria,
                    marca = :marca,
                    modelo = :modelo,
                    numero_serie = :numero_serie,
                    fabricante = :fabricante,
                    data_aquisicao = :data_aquisicao,
                    ano_fabrico = :ano_fabrico,
                    custo_aquisicao = :custo_aquisicao,
                    tipo_entrada = :tipo_entrada,
                    estado = :estado,
                    criticidade = :criticidade,
                    id_localizacao = :id_localizacao,
                    observacoes = :observacoes
                 WHERE id_equipamento = :id_equipamento'
            );

            $stmt->execute([
                ':codigo_interno' => strtoupper($equipamento['codigo_interno']),
                ':designacao' => $equipamento['designacao'],
                ':id_categoria' => (int) $equipamento['id_categoria'],
                ':marca' => $equipamento['marca'],
                ':modelo' => $equipamento['modelo'],
                ':numero_serie' => strtoupper($equipamento['numero_serie']),
                ':fabricante' => $equipamento['fabricante'],
                ':data_aquisicao' => $equipamento['data_aquisicao'] ?: null,
                ':ano_fabrico' => $equipamento['ano_fabrico'] ?: null,
                ':custo_aquisicao' => $custo ?: null,
                ':tipo_entrada' => $equipamento['tipo_entrada'],
                ':estado' => $equipamento['estado'],
                ':criticidade' => $equipamento['criticidade'],
                ':id_localizacao' => (int) $equipamento['id_localizacao'],
                ':observacoes' => $equipamento['observacoes'] ?: null,
                ':id_equipamento' => (int) $id_equipamento
            ]);

            header('Location: equipamento_detalhe.php?id=' . $id_equipamento);
            exit;
        } catch (PDOException $erro) {
            $erros[] = 'Não foi possível guardar as alterações.';
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
<main class="container my-5">
    <div class="mb-4">
        <a href="equipamentos.php" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i>Voltar aos equipamentos
        </a>
        <h1 class="h2 fw-bold mt-3">Editar Equipamento</h1>
    </div>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">
            <strong>Corrige os seguintes erros:</strong>
            <ul class="mb-0">
                <?php foreach ($erros as $erro): ?>
                    <li><?= h($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="equipamento_editar.php?id=<?= h($id_encriptado) ?>" method="post" novalidate>

                <div class="row g-3">
                    <?php
                    $campos_texto = [
                        'codigo_interno' => 'Código interno',
                        'designacao' => 'Designação',
                        'marca' => 'Marca',
                        'modelo' => 'Modelo',
                        'numero_serie' => 'Número de série',
                        'fabricante' => 'Fabricante'
                    ];
                    ?>

                    <?php foreach ($campos_texto as $nome => $rotulo): ?>
                        <div class="col-md-6">
                            <label for="<?= $nome ?>" class="form-label fw-bold">
                                <?= $rotulo ?>
                            </label>
                            <input type="text"
                                   id="<?= $nome ?>"
                                   name="<?= $nome ?>"
                                   class="form-control"
                                   value="<?= h((string) $equipamento[$nome]) ?>"
                                   required>
                        </div>
                    <?php endforeach; ?>

                    <div class="col-md-6">
                        <label for="id_categoria" class="form-label fw-bold">Categoria</label>
                        <select id="id_categoria" name="id_categoria" class="form-select" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= h((string) $categoria['id_categoria']) ?>"
                                    <?= $equipamento['id_categoria'] == $categoria['id_categoria'] ? 'selected' : '' ?>>
                                    <?= h($categoria['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="id_localizacao" class="form-label fw-bold">Localização</label>
                        <select id="id_localizacao" name="id_localizacao" class="form-select" required>
                            <?php foreach ($localizacoes as $localizacao): ?>
                                <option value="<?= h((string) $localizacao['id_localizacao']) ?>"
                                    <?= $equipamento['id_localizacao'] == $localizacao['id_localizacao'] ? 'selected' : '' ?>>
                                    <?= h($localizacao['servico'] . ' - ' . $localizacao['edificio']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="data_aquisicao" class="form-label fw-bold">Data de aquisição</label>
                        <input type="text" id="data_aquisicao" name="data_aquisicao"
                               class="form-control"
                               value="<?= h((string) $equipamento['data_aquisicao']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="ano_fabrico" class="form-label fw-bold">Ano de fabrico</label>
                        <input type="number" id="ano_fabrico" name="ano_fabrico"
                               class="form-control" min="1980" max="<?= date('Y') ?>"
                               value="<?= h((string) $equipamento['ano_fabrico']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="custo_aquisicao" class="form-label fw-bold">Custo de aquisição</label>
                        <input type="text" id="custo_aquisicao" name="custo_aquisicao"
                               class="form-control"
                               value="<?= h((string) $equipamento['custo_aquisicao']) ?>">
                    </div>
                                        <div class="col-md-4">
                        <label for="tipo_entrada" class="form-label fw-bold">
                            Tipo de entrada
                        </label>
                        <select id="tipo_entrada" name="tipo_entrada"
                                class="form-select" required>
                            <?php foreach ($tipos_entrada as $tipo): ?>
                                <option value="<?= h($tipo) ?>"
                                    <?= $equipamento['tipo_entrada'] === $tipo ? 'selected' : '' ?>>
                                    <?= h(ucfirst($tipo)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="estado" class="form-label fw-bold">
                            Estado atual
                        </label>
                        <select id="estado" name="estado"
                                class="form-select" required>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= h($estado) ?>"
                                    <?= $equipamento['estado'] === $estado ? 'selected' : '' ?>>
                                    <?= h(ucfirst(str_replace('_', ' ', $estado))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="criticidade" class="form-label fw-bold">
                            Criticidade
                        </label>
                        <select id="criticidade" name="criticidade"
                                class="form-select" required>
                            <?php foreach ($criticidades as $criticidade): ?>
                                <option value="<?= h($criticidade) ?>"
                                    <?= $equipamento['criticidade'] === $criticidade ? 'selected' : '' ?>>
                                    <?= h(ucfirst(str_replace('_', ' ', $criticidade))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="observacoes" class="form-label fw-bold">
                            Observações
                        </label>
                        <textarea id="observacoes"
                                  name="observacoes"
                                  class="form-control"
                                  rows="4"><?= h((string) $equipamento['observacoes']) ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="equipamento_detalhe.php?id=<?= h((string) $id_equipamento) ?>"
                       class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-2"></i>Guardar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('#data_aquisicao', {
        dateFormat: 'Y-m-d',
        maxDate: 'today'
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
