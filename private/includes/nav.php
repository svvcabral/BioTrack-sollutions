<?php
$activePage = $activePage ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="../assets/img/logo_branco.png" alt="BioTrack Logo" height="30" class="me-2">
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
                    <a class="nav-link <?php echo $activePage === 'fornecedores' ? 'active' : ''; ?>" href="fornecedores.php">Fornecedores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'localizacoes' ? 'active' : ''; ?>" href="localizacoes.php">Localizações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'portal' ? 'active' : ''; ?>" href="backoffice_publico.php">Portal Público</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="../public/logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>