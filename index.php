<?php
session_start();
require_once 'conexao.php';

// Se já estiver logado, vai direto para o dashboard
if (isset($_SESSION['id_atendente'])) {
    header("Location: dashboard.php");
    exit;
}

// Verifica se o banco está vazio (Primeiro Acesso)
$totalAtendentes = (int) $pdo->query("SELECT COUNT(*) FROM atendentes")->fetchColumn();
if ($totalAtendentes === 0) {
    header("Location: primeiro_acesso.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="indexstyle.css">
    <link rel="icon" href="images/logo-fav.png" type="image/png">
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <img src="./images/logo.png" alt="Movva Logo" class="brand-logo-img mb-2">
            <p class="small mb-0" style="color: var(--text-muted);">Painel Operacional de Locação</p>
        </div>

        <div id="alert-box" class="alert alert-danger d-none py-2 small text-center" role="alert">
            <i id="alert-icon" class="bi bi-exclamation-triangle-fill me-1"></i>
            <span id="msg-alerta">Usuário ou senha incorretos!</span>
        </div>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label small" style="color: var(--text-muted);">Usuário / Login</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control" id="usuario" name="login"
                        placeholder="Digite seu login" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label small" style="color: var(--text-muted);">Senha</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-orange w-100">
                Entrar no Sistema <i class="bi bi-arrow-right-short ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <span style="font-size: 0.75rem; color: var(--text-muted);">Movva Fleet System v1.0</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const erro = params.get('erro');
        const msg = params.get('msg');
        const alertBox = document.getElementById('alert-box');
        const msgAlerta = document.getElementById('msg-alerta');
        const alertIcon = document.getElementById('alert-icon');

        if (erro) {
            if (erro === 'inativo') {
                msgAlerta.textContent = 'Acesso bloqueado: colaborador afastado ou desligado.';
            } else if (erro === 'campos_vazios') {
                msgAlerta.textContent = 'Preencha usuário e senha.';
            } else if (erro === 'setup_bloqueado') {
                msgAlerta.textContent = 'Configuração inicial já realizada. Faça login.';
            }
            alertBox.classList.remove('d-none');
        } else if (msg === 'setup_concluido') {
            alertBox.classList.remove('alert-danger', 'd-none');
            alertBox.classList.add('alert-success');
            alertIcon.className = 'bi bi-check-circle-fill me-1';
            msgAlerta.textContent = 'Administrador cadastrado! Faça seu primeiro login.';
        }
    </script>
</body>

</html>
