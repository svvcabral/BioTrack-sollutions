<?php
$pageTitle = 'Fornecedores';
$activePage = 'fornecedores';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    
    <main class="container my-5">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-1">Gestão de Fornecedores</h1>
            <p class="text-muted mb-0">Fabricantes e entidades de assistência técnica autorizadas</p>
        </div>

        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex w-100 mb-3 mb-md-0 me-md-3" style="max-width: 400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" placeholder="Pesquisar por empresa ou NIF...">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary fw-bold"><i class="fas fa-filter me-2"></i>Filtros</button>
                    <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalNovoFornecedor"><i class="fas fa-handshake me-2"></i>Adicionar Fornecedor</button>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Empresa</th>
                                <th class="py-3">NIF</th>
                                <th class="py-3">Categoria</th>
                                <th class="py-3">Contacto Principal</th>
                                <th class="py-3">Estado</th>
                                <th class="px-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-fornecedores"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalNovoFornecedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Registar Fornecedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-fornecedor">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nome da Empresa</label>
                            <input type="text" id="input-empresa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">NIF</label>
                            <input type="text" id="input-nif" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Categoria</label>
                            <select id="input-categoria" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="Fabricante">Fabricante</option>
                                <option value="Assistência Técnica">Assistência Técnica</option>
                                <option value="Distribuidor">Distribuidor</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Email de Contacto</label>
                            <input type="email" id="input-email" class="form-control" required>
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

    <script src="../assets/js/fornecedores.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>