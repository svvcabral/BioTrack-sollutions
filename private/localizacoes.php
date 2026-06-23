<?php
$pageTitle = 'Localizações';
$activePage = 'localizacoes';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    
    
    <main class="container my-5">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-1">Mapeamento Hospitalar</h1>
            <p class="text-muted mb-0">Gestão de edifícios, pisos e serviços clínicos</p>
        </div>

        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-body py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex w-100 mb-3 mb-md-0 me-md-3" style="max-width: 400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" placeholder="Pesquisar serviço ou edifício...">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary fw-bold"><i class="fas fa-filter me-2"></i>Filtros</button>
                    <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalNovaLocalizacao"><i class="fas fa-map-marker-alt me-2"></i>Nova Localização</button>
                </div>
            </div>
        </div>
        <div class="row g-4" id="grid-localizacoes"></div>
    </main>

    <div class="modal fade" id="modalNovaLocalizacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Registar Localização</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-localizacao">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nome do Serviço / Departamento</label>
                            <input type="text" id="input-nome-loc" class="form-control" placeholder="Ex: Ortopedia" required>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark">Edifício</label>
                                <input type="text" id="input-edificio" class="form-control" placeholder="Ex: Edifício Principal" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Piso</label>
                                <input type="text" id="input-piso" class="form-control" placeholder="Ex: Piso 3" required>
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

    <script src="../assets/js/localizacoes.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>