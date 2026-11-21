<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

// Verifica se o usuário logado possui cargo de Gerente
$isGerente = (mb_strtolower(trim($_SESSION['cargo_atendente'] ?? '')) === 'gerente');

$busca = trim($_GET['busca'] ?? '');
if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT * FROM atendentes WHERE nome LIKE :busca OR cpf LIKE :busca OR cargo LIKE :busca ORDER BY id_atendente ASC");
    $stmt->execute([':busca' => "%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM atendentes ORDER BY id_atendente ASC");
}
$atendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colaboradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="painelstyle.css">
    <link rel="icon" href="images/logo-fav.png" type="image/png">
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div>
                <div class="sidebar-header text-center">
                    <img src="./images/logo.png" alt="Movva Logo" class="brand-logo-img">
                </div>

                <ul class="sidebar-menu">
                    <li class="menu-title">Menu Principal</li>

                    <li>
                        <a href="#homeSubmenu" data-bs-toggle="collapse" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-house-door"></i> Home
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse" id="homeSubmenu">
                            <ul class="submenu">
                                <li><a href="novalocacao.php" class="submenu-link"><i
                                            class="bi bi-plus-circle me-1"></i> Nova Locação</a></li>
                                <li><a href="locacoes.php" class="submenu-link"><i class="bi bi-file-text me-1"></i>
                                        Locações</a></li>
                                <li><a href="dashboard.php#estatisticas" class="submenu-link"><i
                                            class="bi bi-graph-up me-1"></i> Estatísticas</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="mt-2">
                        <a href="#veiculosSubmenu" data-bs-toggle="collapse" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-car-front"></i> Veículos
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse" id="veiculosSubmenu">
                            <ul class="submenu">
                                <li><a href="veiculos.php" class="submenu-link">Frota Completa</a></li>
                                <li><a href="veiculos.php?status=Disponível" class="submenu-link">Disponíveis</a></li>
                                <li><a href="veiculos.php?status=Alugado" class="submenu-link">Alugados</a></li>
                                <li><a href="veiculos.php?status=Em manutenção" class="submenu-link">Em Manutenção</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="mt-2">
                        <a href="clientes.php" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-people"></i> Clientes
                            </span>
                        </a>
                    </li>

                    <li class="mt-2">
                        <a href="atendentes.php" class="nav-link active">
                            <span class="nav-link-content">
                                <i class="bi bi-person-badge"></i> Colaboradores
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="border-top pt-3">
                <a href="sobre.php" class="nav-link text-secondary">
                    <span class="nav-link-content">
                        <i class="bi bi-info-circle"></i> Sobre o sistema
                    </span>
                </a>
                <a href="logout.php" class="nav-link text-danger mt-1">
                    <span class="nav-link-content">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </span>
                </a>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main id="content">
            <!-- Cabeçalho -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-person-badge-fill me-2"
                            style="color: var(--brand-orange);"></i>Gestão de Colaboradores</h3>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <form action="atendentes.php" method="GET" class="d-flex gap-2">
                        <input type="text" name="busca" class="form-control" placeholder="Buscar nome, CPF ou cargo..."
                            value="<?php echo htmlspecialchars($busca); ?>" style="border-radius: 10px;">
                        <button type="submit" class="btn btn-outline-secondary" style="border-radius: 10px;">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($busca !== ''): ?>
                            <a href="atendentes.php" class="btn btn-light border" style="border-radius: 10px;"
                                title="Limpar filtro">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </form>

                    <?php if ($isGerente): ?>
                        <button type="button" class="btn btn-orange" data-bs-toggle="modal"
                            data-bs-target="#modalNovoAtendente">
                            <i class="bi bi-plus-lg me-1"></i> Novo Colaborador
                        </button>
                    <?php else: ?>
                        <span class="badge bg-light text-secondary border py-2 px-3">
                            <i class="bi bi-lock me-1"></i> Somente Leitura
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Alertas -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'salvo'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Colaborador cadastrado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'atualizado'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Dados do colaborador atualizados com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'excluido'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-trash-fill me-1"></i> Colaborador removido do sistema.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'duplicado'): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Erro: CPF, E-mail ou Login já está cadastrado em
                        outro colaborador.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'vinculado'): ?>
                    <div class="alert alert-warning alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-shield-exclamation me-1"></i> Não é possível excluir: este colaborador possui locações
                        registradas (altere o status para Desligado).
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'admin_protegido'): ?>
                    <div class="alert alert-warning alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-lock-fill me-1"></i> O usuário administrador principal (ID 1) não pode ser excluído.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'sem_permissao'): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-shield-lock-fill me-1"></i> Acesso negado: apenas colaboradores com cargo de <strong>Gerente</strong> podem cadastrar, editar ou excluir usuários.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Tabela de Colaboradores -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Nome do Atendente</th>
                                <th>CPF</th>
                                <th>Cargo</th>
                                <th>Login</th>
                                <th>Telefone</th>
                                <th>Status</th>
                                <?php if ($isGerente): ?>
                                    <th class="text-end">Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($atendentes) > 0): ?>
                                <?php foreach ($atendentes as $at): ?>
                                    <?php
                                    $badgeStatus = 'bg-secondary';
                                    if ($at['status'] === 'Contratado')
                                        $badgeStatus = 'bg-success';
                                    elseif ($at['status'] === 'Afastado')
                                        $badgeStatus = 'bg-warning text-dark';
                                    elseif ($at['status'] === 'Desligado')
                                        $badgeStatus = 'bg-danger';
                                    ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $at['id_atendente']; ?></td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($at['nome']); ?></div>
                                            <small class="text-secondary"><?php echo htmlspecialchars($at['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($at['cpf']); ?></td>
                                        <td>
                                            <span class="badge <?php echo (mb_strtolower($at['cargo']) === 'gerente') ? 'bg-dark' : 'bg-secondary'; ?>">
                                                <?php echo htmlspecialchars($at['cargo']); ?>
                                            </span>
                                        </td>
                                        <td><span
                                                class="badge bg-light text-dark border"><?php echo htmlspecialchars($at['login']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($at['telefone']); ?></td>
                                        <td><span
                                                class="badge <?php echo $badgeStatus; ?>"><?php echo htmlspecialchars($at['status']); ?></span>
                                        </td>
                                        <?php if ($isGerente): ?>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    title="Editar colaborador"
                                                    onclick='abrirModalEditar(<?php echo json_encode($at, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <?php if ($at['id_atendente'] != 1 && $at['id_atendente'] != $_SESSION['id_atendente']): ?>
                                                    <a href="atendente_excluir.php?id=<?php echo $at['id_atendente']; ?>"
                                                        class="btn btn-sm btn-outline-danger" title="Excluir colaborador"
                                                        onclick="return confirm('Tem certeza que deseja excluir <?php echo htmlspecialchars(addslashes($at['nome'])); ?>?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo $isGerente ? '8' : '7'; ?>" class="text-center py-5 text-secondary">
                                        <i class="bi bi-person-badge fs-2 d-block mb-2 text-muted"></i>
                                        Nenhum colaborador encontrado no banco de dados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php if ($isGerente): ?>
        <!-- Modal: Cadastrar Novo Colaborador -->
        <div class="modal fade" id="modalNovoAtendente" tabindex="-1" aria-labelledby="modalNovoAtendenteLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold" id="modalNovoAtendenteLabel">
                            <i class="bi bi-person-badge-fill me-2" style="color: var(--brand-orange);"></i>Cadastrar Novo
                            Colaborador
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <form action="atendente_salvar.php" method="POST">
                        <div class="modal-body p-4">
                            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Dados Funcionais</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold text-secondary">Nome Completo *</label>
                                    <input type="text" class="form-control" name="nome"
                                        placeholder="Digite o nome completo do colaborador" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">CPF *</label>
                                    <input type="text" class="form-control" name="cpf" placeholder="000.000.000-00"
                                        maxlength="14" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Cargo *</label>
                                    <select class="form-select" name="cargo" required>
                                        <option value="Atendente" selected>Atendente</option>
                                        <option value="Gerente">Gerente</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Data de Admissão *</label>
                                    <input type="date" class="form-control" name="data_admissao"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="Contratado" selected>Contratado</option>
                                        <option value="Afastado">Afastado</option>
                                        <option value="Desligado">Desligado</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">E-mail Corporativo *</label>
                                    <input type="email" class="form-control" name="email" placeholder="nome@movvi.com.br"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Telefone *</label>
                                    <input type="text" class="form-control" name="telefone" placeholder="(22) 99999-9999"
                                        required>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Credenciais de Acesso ao Sistema</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Login de Acesso *</label>
                                    <input type="text" class="form-control" name="login" placeholder="ex: joao.silva"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Senha *</label>
                                    <input type="password" class="form-control" name="senha" placeholder="Defina uma senha"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                                style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</button>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Salvar Colaborador
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Colaborador -->
        <div class="modal fade" id="modalEditarAtendente" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square me-2" style="color: var(--brand-orange);"></i>Editar Colaborador
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <form action="atendente_salvar.php" method="POST">
                        <input type="hidden" name="id_atendente" id="edit_id_atendente">
                        <div class="modal-body p-4">
                            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Dados Funcionais</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold text-secondary">Nome Completo *</label>
                                    <input type="text" class="form-control" id="edit_nome" name="nome" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">CPF *</label>
                                    <input type="text" class="form-control" id="edit_cpf" name="cpf" maxlength="14"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Cargo *</label>
                                    <select class="form-select" id="edit_cargo" name="cargo" required>
                                        <option value="Atendente">Atendente</option>
                                        <option value="Gerente">Gerente</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Data de Admissão *</label>
                                    <input type="date" class="form-control" id="edit_data_admissao" name="data_admissao"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Status *</label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="Contratado">Contratado</option>
                                        <option value="Afastado">Afastado</option>
                                        <option value="Desligado">Desligado</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">E-mail Corporativo *</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Telefone *</label>
                                    <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Credenciais de Acesso</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Login de Acesso *</label>
                                    <input type="text" class="form-control" id="edit_login" name="login" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Senha *</label>
                                    <input type="text" class="form-control" id="edit_senha" name="senha" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                                style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</button>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Atualizar Colaborador
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($isGerente): ?>
    <script>
        function abrirModalEditar(atendente) {
            document.getElementById('edit_id_atendente').value = atendente.id_atendente;
            document.getElementById('edit_nome').value = atendente.nome;
            document.getElementById('edit_cpf').value = atendente.cpf;
            document.getElementById('edit_cargo').value = atendente.cargo;
            document.getElementById('edit_data_admissao').value = atendente.data_admissao;
            document.getElementById('edit_status').value = atendente.status;
            document.getElementById('edit_email').value = atendente.email;
            document.getElementById('edit_telefone').value = atendente.telefone;
            document.getElementById('edit_login').value = atendente.login;
            document.getElementById('edit_senha').value = atendente.senha;

            const modal = new bootstrap.Modal(document.getElementById('modalEditarAtendente'));
            modal.show();
        }
    </script>
    <?php endif; ?>
</body>

</html>