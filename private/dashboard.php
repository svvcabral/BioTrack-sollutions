<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    
    

    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Painel Estatístico</h1>
                <p class="text-muted mb-0">Visão geral do estado operacional do parque tecnológico</p>
            </div>
            <button class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fas fa-download me-2"></i>Exportar Relatório
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Total Equipamentos</h6>
                        <h3 class="fw-bold mb-0 text-dark">244</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Suporte de Vida</h6>
                        <h3 class="fw-bold text-danger mb-0">18</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Fornecedores Ativos</h6>
                        <h3 class="fw-bold mb-0 text-dark">12</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1 fw-bold">Manutenções Hoje</h6>
                        <h3 class="fw-bold mb-0 text-dark">4</h3>
                    </div>
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
<script src="../assets/js/dashboard.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>