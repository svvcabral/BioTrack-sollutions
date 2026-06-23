<?php
$pageTitle = 'Equipamentos';
$activePage = 'equipamentos';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    

    <main class="container my-5">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-1">Inventário de Equipamentos</h1>
            <p class="text-muted mb-0">Gestão global de dispositivos médicos e rastreabilidade</p>
        </div>

        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex w-100 mb-3 mb-md-0 me-md-3" style="max-width: 400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" placeholder="Pesquisar equipamento, marca ou SN...">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary fw-bold"><i class="fas fa-filter me-2"></i>Filtros</button>
                    <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalNovoEquipamento"><i class="fas fa-plus me-2"></i>Registar Equipamento</button>
                </div>
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
                        <tbody id="tabela-equipamentos">
                            <tr>
                                <td class="px-4 py-3 fw-bold text-primary">04.002.00</td>
                                <td class="py-3 fw-medium">Monitor Multiparamétrico</td>
                                <td class="py-3">Philips IntelliVue MP5</td>
                                <td class="py-3">UCI</td>
                                <td class="py-3"><span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Suporte de Vida</span></td>
                                <td class="px-4 py-3 text-end">
                                    <a href="equipamento_detalhe.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Ver</a>
                                </td>
                            </tr>
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
                    <form id="form-equipamento">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Designação</label>
                                <input type="text" id="input-designacao" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Marca / Modelo</label>
                                <input type="text" id="input-marca" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Número de Série (Código)</label>
                                <input type="text" id="input-codigo" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nível de Criticidade</label>
                                <select id="input-criticidade" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="Baixa">Baixa</option>
                                    <option value="Média">Média</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Suporte de Vida">Suporte de Vida</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Localização Atual</label>
                                <select id="input-localizacao" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="Cuidados Intensivos (UCI)">Cuidados Intensivos (UCI)</option>
                                    <option value="Urgência">Urgência</option>
                                    <option value="Bloco Operatório">Bloco Operatório</option>
                                    <option value="Medicina Interna">Medicina Interna</option>
                                </select>
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

    <script src="../assets/js/equipamentos.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>