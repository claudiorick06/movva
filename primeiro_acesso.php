<?php
session_start();
require_once 'conexao.php';

// TRAVA DE SEGURANÇA: Se já existe qualquer colaborador cadastrado, bloqueia esta tela permanentemente
$totalAtendentes = (int) $pdo->query("SELECT COUNT(*) FROM atendentes")->fetchColumn();
if ($totalAtendentes > 0) {
    header("Location: index.php?erro=setup_bloqueado");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($nome === '' || $cpf === '' || $telefone === '' || $email === '' || $login === '' || $senha === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        try {
            $sql = "INSERT INTO atendentes (nome, cpf, telefone, email, cargo, data_admissao, status, login, senha)
                    VALUES (:nome, :cpf, :telefone, :email, 'Gerente', :data_admissao, 'Contratado', :login, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':telefone' => $telefone,
                ':email' => $email,
                ':data_admissao' => date('Y-m-d'),
                ':login' => $login,
                ':senha' => $senha
            ]);

            header("Location: index.php?msg=setup_concluido");
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao criar administrador: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial - Movva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="indexstyle.css">
    <link rel="icon" href="images/logo-fav.png" type="image/png">
</head>

<body>

    <div class="login-card" style="max-width: 520px;">
        <div class="text-center mb-3">
            <img src="./images/logo.png" alt="Movva Logo" class="brand-logo-img mb-2">
            <span class="badge bg-warning text-dark mb-2"><i class="bi bi-shield-lock-fill me-1"></i>Primeiro Acesso do Sistema</span>
            <h5 class="fw-bold mb-1">Criar Administrador Mestre</h5>
            <p class="small mb-0" style="color: var(--text-muted);">
                Nenhum colaborador encontrado. Cadastre o <strong>Gerente</strong> inicial para liberar o sistema.
            </p>
        </div>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger py-2 small text-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form action="primeiro_acesso.php" method="POST">
            <div class="row g-2 mb-2">
                <div class="col-md-7">
                    <label class="form-label small text-secondary mb-1">Nome Completo *</label>
                    <input type="text" class="form-control" name="nome" placeholder="Ex: Mario Rosa" required autofocus>
                </div>
                <div class="col-md-5">
                    <label class="form-label small text-secondary mb-1">CPF *</label>
                    <input type="text" class="form-control" name="cpf" placeholder="Somente números" maxlength="14" required>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <label class="form-label small text-secondary mb-1">E-mail Corporativo *</label>
                    <input type="email" class="form-control" name="email" placeholder="gerente@movva.com.br" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-secondary mb-1">Telefone *</label>
                    <input type="text" class="form-control" name="telefone" placeholder="(xx) xxxxx-xxxx" required>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label small text-secondary mb-1">Login de Acesso *</label>
                    <input type="text" class="form-control" name="login" placeholder="Ex: admin" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-secondary mb-1">Senha *</label>
                    <input type="password" class="form-control" name="senha" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-orange w-100">
                Concluir Configuração e Continuar <i class="bi bi-check-circle ms-1"></i>
            </button>
        </form>
    </div>

</body>

</html>