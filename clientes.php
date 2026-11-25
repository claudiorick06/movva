<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

$busca = trim($_GET['busca'] ?? '');
if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT * FROM cliente WHERE nome LIKE :busca OR cpf LIKE :busca ORDER BY id_cliente DESC");
    $stmt->execute([':busca' => "%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM cliente ORDER BY id_cliente DESC");
}
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="painelstyle.css">
    <link rel="icon" href="images/logo.png" type="image/png">
</head>

<body>

    <div class="wrapper">
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
                        <a href="clientes.php" class="nav-link active">
                            <span class="nav-link-content">
                                <i class="bi bi-people"></i> Clientes
                            </span>
                        </a>
                    </li>

                    <li class="mt-2">
                        <a href="atendentes.php" class="nav-link">
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

        <main id="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"
                            style="color: var(--brand-orange);"></i>Base de Clientes</h3>
                    <p class="text-secondary small mb-0">Clientes cadastrados para locação na Movva.</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="clientes.php" method="GET" class="d-flex gap-2">
                        <input type="text" name="busca" class="form-control" placeholder="Buscar nome ou CPF..."
                            value="<?php echo htmlspecialchars($busca); ?>" style="border-radius: 10px;">
                        <button type="submit" class="btn btn-outline-secondary" style="border-radius: 10px;">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($busca !== ''): ?>
                            <a href="clientes.php" class="btn btn-light border" style="border-radius: 10px;"
                                title="Limpar filtro">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="btn btn-orange" data-bs-toggle="modal"
                        data-bs-target="#modalNovoCliente">
                        <i class="bi bi-plus-lg me-1"></i> Novo Cliente
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'salvo'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Cliente cadastrado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'atualizado'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Dados do cliente atualizados com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'excluido'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-trash-fill me-1"></i> Cliente removido do sistema.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'duplicado'): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Erro: CPF já está cadastrado em outro cliente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'vinculado'): ?>
                    <div class="alert alert-warning alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-shield-exclamation me-1"></i> Não é possível excluir: este cliente possui locações
                        registradas no histórico.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Nome Completo</th>
                                <th>CPF (Chave Operação)</th>
                                <th>Telefone</th>
                                <th>Cidade / UF</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $cli): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $cli['id_cliente']; ?></td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($cli['nome']); ?></div>
                                            <small class="text-secondary"><?php echo htmlspecialchars($cli['email']); ?></small>
                                        </td>
                                        <td class="font-monospace"><?php echo htmlspecialchars($cli['cpf']); ?></td>
                                        <td><?php echo htmlspecialchars($cli['telefone']); ?></td>
                                        <td><?php echo htmlspecialchars($cli['cidade'] . '/' . $cli['estado']); ?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Editar cliente"
                                                onclick='abrirModalEditar(<?php echo json_encode($cli, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="cliente_excluir.php?id=<?php echo $cli['id_cliente']; ?>"
                                                class="btn btn-sm btn-outline-danger" title="Excluir cliente"
                                                onclick="return confirm('Tem certeza que deseja excluir o cliente <?php echo htmlspecialchars(addslashes($cli['nome'])); ?>?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-secondary">
                                        <i class="bi bi-people fs-2 d-block mb-2 text-muted"></i>
                                        Nenhum cliente encontrado no banco de dados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Cadastrar Novo Cliente -->
    <div class="modal fade" id="modalNovoCliente" tabindex="-1" aria-labelledby="modalNovoClienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold" id="modalNovoClienteLabel">
                        <i class="bi bi-person-plus-fill me-2" style="color: var(--brand-orange);"></i>Cadastrar Novo
                        Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="cliente_salvar.php" method="POST">
                    <div class="modal-body p-4">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Dados Pessoais</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold text-secondary">Nome Completo *</label>
                                <input type="text" class="form-control" name="nome" placeholder="Digite o nome completo"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">CPF (somente números)
                                    *</label>
                                <input type="text" class="form-control" name="cpf" placeholder="00000000000"
                                    maxlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/\D/g, '')"
                                    required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Telefone (somente números)
                                    *</label>
                                <input type="text" class="form-control" name="telefone" placeholder="22999999999"
                                    maxlength="11" pattern="\d{10,11}"
                                    oninput="this.value = this.value.replace(/\D/g, '')" required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-secondary">E-mail *</label>
                                <input type="email" class="form-control" name="email" placeholder="cliente@email.com"
                                    required>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Endereço Residencial</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">CEP *</label>
                                <input type="text" class="form-control" name="cep" placeholder="00000-000" maxlength="9"
                                    required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-secondary">Logradouro *</label>
                                <input type="text" class="form-control" name="logradouro" placeholder="Rua, Avenida..."
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-secondary">Número *</label>
                                <input type="text" class="form-control" name="numero" placeholder="123" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Bairro *</label>
                                <input type="text" class="form-control" name="bairro" placeholder="Bairro" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Cidade *</label>
                                <input type="text" class="form-control" name="cidade" placeholder="Cidade" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-secondary">UF *</label>
                                <input type="text" class="form-control text-uppercase" name="estado" placeholder="RJ"
                                    maxlength="2" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</button>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-check-circle me-1"></i> Salvar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Cliente -->
    <div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2" style="color: var(--brand-orange);"></i>Editar Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="cliente_salvar.php" method="POST">
                    <input type="hidden" name="id_cliente" id="edit_id_cliente">
                    <div class="modal-body p-4">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Dados Pessoais</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold text-secondary">Nome Completo *</label>
                                <input type="text" class="form-control" id="edit_nome" name="nome" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">CPF (somente números)
                                    *</label>
                                <input type="text" class="form-control" id="edit_cpf" name="cpf" maxlength="11"
                                    pattern="\d{11}" oninput="this.value = this.value.replace(/\D/g, '')" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Telefone (somente números)
                                    *</label>
                                <input type="text" class="form-control" id="edit_telefone" name="telefone"
                                    maxlength="11" pattern="\d{10,11}"
                                    oninput="this.value = this.value.replace(/\D/g, '')" required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-secondary">E-mail *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Endereço Residencial</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">CEP *</label>
                                <input type="text" class="form-control" id="edit_cep" name="cep" maxlength="9" required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-secondary">Logradouro *</label>
                                <input type="text" class="form-control" id="edit_logradouro" name="logradouro" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-secondary">Número *</label>
                                <input type="text" class="form-control" id="edit_numero" name="numero" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Bairro *</label>
                                <input type="text" class="form-control" id="edit_bairro" name="bairro" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Cidade *</label>
                                <input type="text" class="form-control" id="edit_cidade" name="cidade" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-secondary">UF *</label>
                                <input type="text" class="form-control text-uppercase" id="edit_estado" name="estado"
                                    maxlength="2" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</button>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-check-circle me-1"></i> Atualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModalEditar(cliente) {
            document.getElementById('edit_id_cliente').value = cliente.id_cliente;
            document.getElementById('edit_nome').value = cliente.nome;
            document.getElementById('edit_cpf').value = cliente.cpf;
            document.getElementById('edit_telefone').value = cliente.telefone;
            document.getElementById('edit_email').value = cliente.email;
            document.getElementById('edit_cep').value = cliente.cep;
            document.getElementById('edit_logradouro').value = cliente.logradouro;
            document.getElementById('edit_numero').value = cliente.numero;
            document.getElementById('edit_bairro').value = cliente.bairro;
            document.getElementById('edit_cidade').value = cliente.cidade;
            document.getElementById('edit_estado').value = cliente.estado;

            const modal = new bootstrap.Modal(document.getElementById('modalEditarCliente'));
            modal.show();
        }
    </script>
</body>

</html>