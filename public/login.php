<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

iniciar_sessao();

$erros = $_SESSION['validation_errors'] ?? [];
$erroServidor = $_SESSION['server_error'] ?? '';

unset($_SESSION['validation_errors'], $_SESSION['server_error']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-in - BioTrack solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/1241381.css?v=20260624b">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
        }
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: none;
            width: 100%;
            max-width: 450px;
            background-color: #ffffff;
        }
        .form-custom-input {
            background-color: #e9ecef !important;
            border: none !important;
            border-radius: 8px !important;
            color: #333333 !important;
            padding: 0.75rem 1rem;
        }
        .form-custom-input:focus {
            background-color: #e2e5e7 !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 158, 181, 0.15) !important;
        }
        .btn-login {
            border-radius: 50px !important;
            padding: 0.75rem 2rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(0, 158, 181, 0.3);
        }
    </style>
</head>
<body>

    <div class="container login-container">
        <div class="card login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <a href="index.php" class="d-flex align-items-center justify-content-center text-decoration-none mb-2">
                    <img src="../assets/img/logo_ciano.png?v=20260624b" alt="Logótipo BioTrack"
                         class="logo-biotrack logo-biotrack-login me-2">
                    <span class="fw-bold fs-3 text-dark" style="letter-spacing: -1px;">BioTrack</span>
                </a>
                <p class="text-muted small">Sistemas de Informação Hospitalar</p>
            </div>

            <?php if (!empty($erros) || $erroServidor !== ''): ?>
    <div class="alert alert-danger small" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>

        <?php foreach ($erros as $erro): ?>
            <div><?php echo htmlspecialchars($erro); ?></div>
        <?php endforeach; ?>

        <?php if ($erroServidor !== ''): ?>
            <div><?php echo htmlspecialchars($erroServidor); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>

            <form id="form-login" action="../private/processa_login.php" method="post">
                <div class="mb-3">
                    <label for="inputEmail" class="form-label fw-bold text-dark small">Utilizador ou Email:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-user"></i></span>
                        <input type="email" class="form-control form-custom-input" id="inputEmail" name="text_username" placeholder="ex: engenharia@biotrack.pt" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="inputPassword" class="form-label fw-bold text-dark small">Palavra-passe:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control form-custom-input" id="inputPassword" name="text_password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-login btn-lg">Entrar no Sistema</button>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none small text-muted fw-medium">
                        <i class="fas fa-arrow-left me-1"></i> Voltar à página inicial
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
