<?php
require_once __DIR__ . '/../private/includes/database.php';

function h(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$conteudos = [
    'hero' => ['titulo' => 'A nova era da gestão de Tecnologia Médica', 'conteudo' => 'Mapeamento em tempo real, gestão de ciclo de vida e mitigação de falhas para dispositivos médicos de suporte crítico.'],
    'visao' => ['titulo' => 'Da Engenharia Biomédica para a Prática Clínica', 'conteudo' => 'Como estudante de Engenharia Biomédica no ISEP, desenhei o BioTrack não apenas como um repositório de dados, mas como uma ponte crítica entre a tecnologia e o cuidado ao paciente.'],
    'autor_nome' => ['conteudo' => 'Sofia'],
    'autor_papel' => ['conteudo' => 'Autora do Projeto • SIBDAS 2026'],
    'contacto_email' => ['conteudo' => 'suporte@biotrack.pt'],
    'contacto_telefone' => ['conteudo' => '+351 228 340 500'],
    'contacto_morada' => ['conteudo' => "Rua Dr. António Bernardino de Almeida\n4200-072, Porto\nPortugal"],
    'horario_semana' => ['conteudo' => '2ª a 6ª Feira: 09h — 17h'],
    'horario_sabado' => ['conteudo' => 'Sábados: 09h — 13h'],
    'horario_domingo' => ['conteudo' => 'Domingos / Feriados: Encerrado'],
];

try {
    $registos = ligar_bd()->query(
        'SELECT chave, titulo, conteudo FROM conteudos_publicos'
    )->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    foreach ($registos as $chave => $registo) {
        $conteudos[$chave] = array_merge($conteudos[$chave] ?? [], $registo);
    }
} catch (PDOException $erro) {
    error_log('Falha ao carregar conteúdos públicos: ' . $erro->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioTrack solutions - Gestão Hospitalar Inteligente</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_ciano.png?v=20260624c">
    <link rel="apple-touch-icon" href="../assets/img/logo_ciano.png?v=20260624c">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/1241381.css?v=20260624b">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .navbar { padding: 1.5rem 0; background-color: #f8f9fa !important; }
        .hero-section { padding: 7rem 0 5rem; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; }
        .hero-title { font-size: 3.5rem; font-weight: 800; color: #1a1a1a !important; line-height: 1.2; letter-spacing: -1px; }
        .hero-subtitle { font-size: 1.25rem; color: #6c757d; margin-top: 1.5rem; margin-bottom: 2.5rem; line-height: 1.6; }
        .feature-icon { font-size: 2.5rem; color: var(--bs-primary); margin-bottom: 1.5rem; }
        .section-padding { padding: 5rem 0; }
        .btn-lg { padding: 0.8rem 2.5rem; font-size: 1.05rem; border-radius: 50px !important; }
        .stats-number { font-size: 3.5rem; font-weight: 800; color: var(--bs-primary) !important; }
        
        .feature-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            padding: 2rem 1rem;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            background-color: #ffffff !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        }
        
        .form-custom-input {
            background-color: #e9ecef !important;
            border: none !important;
            border-radius: 8px !important;
            color: #333333 !important;
        }
        .form-custom-input:focus {
            background-color: #e2e5e7 !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 158, 181, 0.15) !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="../assets/img/logo_ciano.png?v=20260624b" alt="Logótipo BioTrack"
                     class="logo-biotrack me-2">
                <span class="fw-bold fs-4 text-dark" style="letter-spacing: -1px;">BioTrack <span class="fw-normal text-muted fs-5">solutions</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#inicio">Início</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#produtos">Sobre nós</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#visao">A empresa</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#contacto">Saber mais</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#contactos-rodape">Contacto</a></li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-primary fw-bold ms-3 px-4" style="border-radius: 50px;">
                            Sign-in / Registo
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="inicio" class="hero-section text-center text-md-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="hero-title"><?= h($conteudos['hero']['titulo']) ?></h1>
                    <p class="hero-subtitle"><?= h($conteudos['hero']['conteudo']) ?></p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-md-start mt-4">
                        <a href="login.php" class="btn btn-primary btn-lg fw-bold shadow-sm">Aceder ao Sistema</a>
                        <a href="#contacto" class="btn btn-outline-secondary btn-lg fw-bold">Contactar</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="bg-white p-2 shadow-lg" style="border-radius: 24px;">
                        <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Equipamento Médico Alta Tecnologia" class="img-fluid" style="border-radius: 18px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="produtos" class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5 pb-3">
                <h2 class="fw-bold mb-3" style="font-size: 2.5rem; letter-spacing: -0.5px;">Arquitetura do Sistema</h2>
                <p class="text-muted fs-5">Módulos desenvolvidos para otimizar o fluxo de trabalho da engenharia clínica.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-transparent feature-card text-center text-md-start">
                        <div class="card-body px-4">
                            <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle mb-4">
                                <i class="fas fa-microchip fs-3 text-primary"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Gestão de Ativos</h4>
                            <p class="text-muted">Classificação avançada de inventário baseada em níveis de criticidade e rastreabilidade total do ciclo de vida dos equipamentos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-transparent feature-card text-center text-md-start">
                        <div class="card-body px-4">
                            <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle mb-4">
                                <i class="fas fa-network-wired fs-3 text-primary"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Mapeamento Espacial</h4>
                            <p class="text-muted">Integração do parque tecnológico por edifícios e serviços clínicos, garantindo a localização exata de dispositivos de urgência.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-transparent feature-card text-center text-md-start">
                        <div class="card-body px-4">
                            <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle mb-4">
                                <i class="fas fa-tools fs-3 text-primary"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Rede de Suporte</h4>
                            <p class="text-muted">Centralização de contactos de fabricantes e entidades de assistência técnica para resposta imediata a tempos de inatividade.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding bg-light border-bottom">
        <div class="container text-center">
            <div class="row g-4 justify-content-center">
                <div class="col-md-3">
                    <h2 class="stats-number">350+</h2>
                    <p class="fw-bold text-dark text-uppercase small" style="letter-spacing: 1px;">Equipamentos</p>
                </div>
                <div class="col-md-3">
                    <h2 class="stats-number">24/7</h2>
                    <p class="fw-bold text-dark text-uppercase small" style="letter-spacing: 1px;">Prontidão</p>
                </div>
                <div class="col-md-3">
                    <h2 class="stats-number">100%</h2>
                    <p class="fw-bold text-dark text-uppercase small" style="letter-spacing: 1px;">Conformidade</p>
                </div>
            </div>
        </div>
    </section>

    <section id="visao" class="py-5" style="background-color: #009eb5;">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <i class="fas fa-quote-left text-white opacity-50 mb-4" style="font-size: 3rem;"></i>
                    <h2 class="fw-bold text-white mb-4" style="letter-spacing: -0.5px;"><?= h($conteudos['visao']['titulo']) ?></h2>
                    <p class="text-white fs-5 lh-lg mb-0" style="font-weight: 300;">
                        “<?= h($conteudos['visao']['conteudo']) ?>”
                    </p>
                    <hr class="border-white opacity-25 my-5 mx-auto" style="width: 100px;">
                    <div class="text-white">
                        <h6 class="fw-bold text-uppercase letter-spacing-1 mb-1"><?= h($conteudos['autor_nome']['conteudo']) ?></h6>
                        <p class="small opacity-75 mb-0"><?= h($conteudos['autor_papel']['conteudo']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3 text-dark" style="font-size: 2.5rem; letter-spacing: -0.5px;">Fale Connosco</h2>
                <p class="text-muted fs-5 w-75 mx-auto">Interessado na arquitetura do sistema? Envie uma mensagem para testar a demonstração.</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm" style="background-color: #f8f9fa; border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">
                            
                            <div id="alerta-sucesso" class="alert alert-success d-none mb-4 text-center fw-bold shadow-sm" role="alert" style="border-radius: 10px;">
                                <i class="fas fa-check-circle me-2 fs-5"></i> Mensagem enviada com sucesso. Entraremos em contacto brevemente.
                            </div>

                            <form id="form-contacto">
                                <div class="mb-4">
                                    <label for="nomeContacto" class="form-label fw-bold text-dark">Nome:</label>
                                    <input type="text" class="form-control form-control-lg form-custom-input" id="nomeContacto" placeholder="Ex: Centro Hospitalar do Porto" required>
                                </div>
                                <div class="mb-4">
                                    <label for="emailContacto" class="form-label fw-bold text-dark">Email:</label>
                                    <input type="email" class="form-control form-control-lg form-custom-input" id="emailContacto" placeholder="Ex: engenharia.biomedica@hospital.pt" required>
                                </div>
                                <div class="mb-4">
                                    <label for="mensagemContacto" class="form-label fw-bold text-dark">Mensagem:</label>
                                    <textarea class="form-control form-control-lg form-custom-input" id="mensagemContacto" rows="4" placeholder="Ex: Gostaria de solicitar mais informações sobre a plataforma..." required></textarea>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-5" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(0, 158, 181, 0.3);">Enviar Mensagem</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="contactos-rodape" class="bg-dark py-5">
        <div class="container">
            <div class="row text-center text-md-start">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="text-uppercase fw-bold mb-4" style="letter-spacing: 1px; color: white !important;">Localização</h6>
                    <p class="text-white-50 mb-0"><?= nl2br(h($conteudos['contacto_morada']['conteudo'])) ?></p>
                </div>
                
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="text-uppercase fw-bold mb-4" style="letter-spacing: 1px; color: white !important;">Suporte Técnico</h6>
                    <p class="text-white-50 mb-1"><?= h($conteudos['horario_semana']['conteudo']) ?></p>
                    <p class="text-white-50 mb-1"><?= h($conteudos['horario_sabado']['conteudo']) ?></p>
                    <p class="text-white-50 mb-0"><?= h($conteudos['horario_domingo']['conteudo']) ?></p>
                </div>
                
                <div class="col-md-4">
                    <h6 class="text-uppercase fw-bold mb-4" style="letter-spacing: 1px; color: white !important;">Contactos</h6>
                    <p class="text-white-50 mb-1">Email: <?= h($conteudos['contacto_email']['conteudo']) ?></p>
                    <p class="text-white-50 mb-0">Telefone: <?= h($conteudos['contacto_telefone']['conteudo']) ?></p>
                </div>
            </div>
            
            <hr class="mt-5 mb-4 border-secondary">
            <div class="text-center">
                <p class="fw-bold mb-1" style="color: white !important;">© 2026 BioTrack solutions. Todos os direitos reservados.</p>
                <p class="text-white-50 small mb-0">Projeto desenvolvido em SIBDAS - Engenharia Biomédica, ISEP</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/1241381.js"></script>
</body>
</html>
