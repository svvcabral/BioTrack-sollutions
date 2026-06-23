<?php
$pageTitle = 'Portal Público';
$activePage = 'portal';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>
    
    
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Gestão do Portal Público</h1>
                <p class="text-muted mb-0">Edite os textos e informações apresentados na página inicial (Front Office)</p>
            </div>
            <a href="../public/index.php" target="_blank" class="btn btn-outline-secondary fw-bold">
                <i class="fas fa-external-link-alt me-2"></i>Ver Site Público
            </a>
        </div>

        <form id="form-backoffice">
            <div class="row g-4">
                <div class="col-lg-8">
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-heading me-2 text-primary"></i>Banner Principal (Hero Section)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Título Principal</label>
                                <input type="text" class="form-control form-custom-input" value="A nova era da gestão de Tecnologia Médica" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-dark">Subtítulo / Descrição</label>
                                <textarea class="form-control form-custom-input" rows="3" required>Mapeamento em tempo real, gestão de ciclo de vida e mitigação de falhas para dispositivos médicos de suporte crítico.</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-quote-left me-2 text-primary"></i>Secção "A Visão"</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Texto do Manifesto</label>
                                <textarea class="form-control form-custom-input" rows="4" required>"Como estudante de Engenharia Biomédica no ISEP, desenhei o BioTrack não apenas como um repositório de dados, mas como uma ponte crítica entre a tecnologia e o cuidado ao paciente. O objetivo deste projeto é provar que uma gestão de informação bem estruturada garante que o equipamento certo está pronto a salvar vidas, no momento exato em que é preciso."</textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Nome do Autor</label>
                                    <input type="text" class="form-control form-custom-input" value="Sofia" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Cargo / Papel</label>
                                    <input type="text" class="form-control form-custom-input" value="Autora do Projeto • SIBDAS 2026" required>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-address-card me-2 text-primary"></i>Contactos e Rodapé</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Email de Suporte</label>
                                <input type="email" class="form-control form-custom-input" value="suporte@biotrack.pt" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Telefone Geral</label>
                                <input type="text" class="form-control form-custom-input" value="+351 228 340 500" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Morada</label>
                                <textarea class="form-control form-custom-input" rows="2" required>Rua Dr. António Bernardino de Almeida, 4200-072, Porto</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-dark">Horário Técnico</label>
                                <input type="text" class="form-control form-custom-input" value="2ª a 6ª Feira: 09h — 17h" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Alterações
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </main>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toastSucesso" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold fs-6">
                    <i class="fas fa-check-circle me-2"></i> Conteúdos atualizados com sucesso!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    
    <script>
        document.getElementById('form-backoffice').addEventListener('submit', function(event) {
            event.preventDefault(); 
            const toastEl = document.getElementById('toastSucesso');
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        });
    </script>
<?php include __DIR__ . '/includes/footer.php'; ?>