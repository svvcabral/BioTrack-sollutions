<?php
$activePage = $activePage ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="../assets/img/logo_branco.png?v=20260624b" alt="Logótipo BioTrack"
                 class="logo-biotrack me-2">
            <span class="fw-bold text-white"><?php echo APP_NAME; ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="sidebarMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'equipamentos' ? 'active' : ''; ?>" href="equipamentos.php">Equipamentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'localizacoes' ? 'active' : ''; ?>" href="localizacoes.php">Localizações</a>
                </li>
                <?php if (utilizador_administrador()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'fornecedores' ? 'active' : ''; ?>" href="fornecedores.php">Fornecedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'portal' ? 'active' : ''; ?>" href="backoffice_publico.php">Portal Público</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle text-white fw-bold bg-transparent border-0"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['nome_utilizador'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        (<?= ($_SESSION['perfil'] ?? '') === 'tecnico' ? 'Técnico' : 'Administrador' ?>)
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item py-2" href="alterar_palavra_passe.php">
                                <i class="fas fa-key text-primary me-2"></i>Alterar palavra-passe
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="../public/logout.php">
                                <i class="fas fa-sign-out-alt text-danger me-2"></i>Sair da conta
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
