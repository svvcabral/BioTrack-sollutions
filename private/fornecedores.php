<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - BioTrack solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/1241381.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="../assets/img/logo_branco.png" alt="BioTrack Logo" height="30" class="me-2">
                <span class="fw-bold text-white">BioTrack</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="sidebarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="equipamentos.php">Equipamentos</a></li>
                    <li class="nav-item"><a class="nav-link active" href="fornecedores.php">Fornecedores</a></li>
                    <li class="nav-item"><a class="nav-link" href="localizacoes.php">Localizações</a></li>
                    <li class="nav-item"><a class="nav-link" href="backoffice_publico.php">Portal Público</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="../public/index.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/fornecedores.js"></script>
</body>
</html>