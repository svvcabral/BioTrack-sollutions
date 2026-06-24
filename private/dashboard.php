<?php
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_autenticado();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$erro_sistema = '';
$metricas = [
    'total' => 0,
    'ativos' => 0,
    'manutencao' => 0,
    'inativos' => 0,
    'garantias_expiradas' => 0,
    'sem_documentacao' => 0,
];
$por_criticidade = [];
$por_servico = [];

try {
    $ligacao = ligar_bd();

    $linha = $ligacao->query(
        "SELECT
            COUNT(*) AS total,
            SUM(estado = 'ativo') AS ativos,
            SUM(estado = 'em_manutencao') AS manutencao,
            SUM(estado = 'inativo') AS inativos
         FROM equipamentos
         WHERE ativo = 1"
    )->fetch();

    foreach (['total', 'ativos', 'manutencao', 'inativos'] as $chave) {
        $metricas[$chave] = (int) ($linha[$chave] ?? 0);
    }

    $metricas['garantias_expiradas'] = (int) $ligacao->query(
        "SELECT COUNT(DISTINCT g.id_equipamento)
         FROM garantias g
         INNER JOIN equipamentos e ON e.id_equipamento = g.id_equipamento
         WHERE e.ativo = 1 AND g.data_fim < CURDATE()"
    )->fetchColumn();

    $metricas['sem_documentacao'] = (int) $ligacao->query(
        "SELECT COUNT(*)
         FROM equipamentos e
         WHERE e.ativo = 1
           AND NOT EXISTS (
               SELECT 1 FROM documentos d
               WHERE d.id_equipamento = e.id_equipamento
           )"
    )->fetchColumn();

    $por_criticidade = $ligacao->query(
        "SELECT criticidade, COUNT(*) AS total
         FROM equipamentos
         WHERE ativo = 1
         GROUP BY criticidade
         ORDER BY FIELD(criticidade, 'baixa', 'media', 'alta', 'suporte_de_vida')"
    )->fetchAll();

    $por_servico = $ligacao->query(
        "SELECT l.servico, COUNT(e.id_equipamento) AS total
         FROM localizacoes l
         LEFT JOIN equipamentos e
           ON e.id_localizacao = l.id_localizacao AND e.ativo = 1
         WHERE l.ativo = 1
         GROUP BY l.id_localizacao, l.servico
         ORDER BY total DESC, l.servico"
    )->fetchAll();
} catch (PDOException $erro) {
    $erro_sistema = 'Não foi possível carregar os indicadores: ' . $erro->getMessage();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    
    

    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Painel Estatístico</h1>
                <p class="text-muted mb-0">Visão geral do estado operacional do parque tecnológico</p>
            </div>
        </div>

        <?php if ($erro_sistema !== ''): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Total Equipamentos</h6>
                        <h3 class="fw-bold mb-0 text-dark"><?= $metricas['total'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Equipamentos Ativos</h6>
                        <h3 class="fw-bold text-success mb-0"><?= $metricas['ativos'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Em Manutenção</h6>
                        <h3 class="fw-bold text-warning mb-0"><?= $metricas['manutencao'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Equipamentos Inativos</h6>
                        <h3 class="fw-bold text-secondary mb-0"><?= $metricas['inativos'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <h6 class="text-muted text-uppercase small mb-1 fw-bold">Garantias Expiradas</h6>
                    <h3 class="fw-bold text-danger mb-0"><?= $metricas['garantias_expiradas'] ?></h3>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <h6 class="text-muted text-uppercase small mb-1 fw-bold">Sem Documentação</h6>
                    <h3 class="fw-bold text-danger mb-0"><?= $metricas['sem_documentacao'] ?></h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h6 class="fw-bold text-uppercase text-muted mb-0">Distribuição por Criticidade</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div style="height: 250px; width: 100%;">
                            <canvas id="chartCriticidade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h6 class="fw-bold text-uppercase text-muted mb-0">Equipamentos por Localização</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div style="height: 250px; width: 100%;">
                            <canvas id="chartLocalizacoes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const criticidade = <?= json_encode($por_criticidade, JSON_UNESCAPED_UNICODE) ?>;
        const servicos = <?= json_encode($por_servico, JSON_UNESCAPED_UNICODE) ?>;
        const textos = {
            baixa: 'Baixa',
            media: 'Média',
            alta: 'Alta',
            suporte_de_vida: 'Suporte de vida'
        };

        new Chart(document.getElementById('chartCriticidade'), {
            type: 'doughnut',
            data: {
                labels: criticidade.map(item => textos[item.criticidade] || item.criticidade),
                datasets: [{
                    data: criticidade.map(item => Number(item.total)),
                    backgroundColor: ['#20c997', '#0dcaf0', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } },
                cutout: '68%'
            }
        });

        new Chart(document.getElementById('chartLocalizacoes'), {
            type: 'bar',
            data: {
                labels: servicos.map(item => item.servico),
                datasets: [{
                    label: 'Equipamentos',
                    data: servicos.map(item => Number(item.total)),
                    backgroundColor: '#009eb5',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    });
    </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
