<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhe de Equipamento - BioTrack solutions</title>
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
                    <li class="nav-item"><a class="nav-link active" href="equipamentos.php">Equipamentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="fornecedores.php">Fornecedores</a></li>
                    <li class="nav-item"><a class="nav-link" href="localizacoes.php">Localizações</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="equipamentos.php" class="text-decoration-none text-muted mb-2 d-inline-block fw-medium">
                    <i class="fas fa-arrow-left me-1"></i> Voltar à lista de equipamentos
                </a>
                <h1 class="h2 fw-bold mb-1">Monitor Multiparamétrico</h1>
                <p class="text-muted mb-0">Código de Inventário: <span class="fw-bold text-dark">04.002.00</span></p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary fw-bold"><i class="fas fa-edit me-2"></i>Editar</button>
                <button class="btn btn-outline-secondary fw-bold"><i class="fas fa-print me-2"></i>Imprimir Ficha</button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informação Geral</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p class="text-muted small mb-1 fw-bold text-uppercase">Marca / Modelo</p>
                                <p class="fs-5 mb-0 text-dark">Philips IntelliVue MP5</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1 fw-bold text-uppercase">Número de Série (SN)</p>
                                <p class="fs-5 mb-0 text-dark">MP5-2022-45873</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1 fw-bold text-uppercase">Categoria</p>
                                <p class="fs-5 mb-0 text-dark">Monitorização</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1 fw-bold text-uppercase">Fornecedor Principal</p>
                                <p class="fs-5 mb-0 text-primary fw-medium"><i class="fas fa-external-link-alt me-1 small"></i> MedTec Portugal, Lda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-heartbeat me-2 text-primary"></i>Estado Operacional</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <p class="text-muted small mb-2 fw-bold text-uppercase">Criticidade Clínica</p>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-bold fs-6">
                                <i class="fas fa-exclamation-triangle me-1"></i> Suporte de Vida
                            </span>
                        </div>
                        <div class="mb-4">
                            <p class="text-muted small mb-2 fw-bold text-uppercase">Estado Atual</p>
                            <span class="badge bg-success px-3 py-2 rounded-pill fw-bold fs-6">Ativo</span>
                        </div>
                        <div>
                            <p class="text-muted small mb-1 fw-bold text-uppercase">Localização Física</p>
                            <p class="mb-0 text-dark fw-medium"><i class="fas fa-map-marker-alt text-danger me-1"></i> Unidade de Cuidados Intensivos (UCI)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-folder-open me-2 text-primary"></i>Dossiê do Equipamento</h5>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="accordionDossier">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDocs">
                                Documentação Técnica e Certificados
                            </button>
                        </h2>
                        <div id="collapseDocs" class="accordion-collapse collapse show" data-bs-parent="#accordionDossier">
                            <div class="accordion-body p-4 bg-light">
                                <ul class="list-group list-group-flush border rounded">
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                        <div>
                                            <i class="fas fa-file-pdf text-danger me-2 fs-5"></i>
                                            <span class="fw-medium text-dark">Manual_Utilizador_MP5.pdf</span>
                                            <span class="text-muted small ms-2">(Adicionado a 12/03/2023)</span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i></button>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                        <div>
                                            <i class="fas fa-file-contract text-info me-2 fs-5"></i>
                                            <span class="fw-medium text-dark">Certificado_Calibracao_2025.pdf</span>
                                            <span class="text-muted small ms-2">(Válido até 15/01/2026)</span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i></button>
                                    </li>
                                </ul>
                                <button class="btn btn-sm btn-primary mt-3 fw-bold"><i class="fas fa-upload me-2"></i>Anexar Documento</button>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGarantia">
                                Garantias e Contratos de Manutenção
                            </button>
                        </h2>
                        <div id="collapseGarantia" class="accordion-collapse collapse" data-bs-parent="#accordionDossier">
                            <div class="accordion-body p-4 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="bg-white p-3 border rounded">
                                            <p class="text-muted small mb-1 fw-bold">Data de Aquisição</p>
                                            <p class="mb-0 fw-medium">10 de Janeiro de 2023</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-3 border rounded">
                                            <p class="text-muted small mb-1 fw-bold">Fim da Garantia Geral</p>
                                            <p class="mb-0 fw-medium text-danger">10 de Janeiro de 2026</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-3 border rounded">
                                            <p class="text-muted small mb-1 fw-bold">Contrato de Manutenção</p>
                                            <p class="mb-0 fw-medium text-success">Ativo (Preventiva Anual)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>