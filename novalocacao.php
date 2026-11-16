<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

// Busca o CPF do atendente logado para pré-preencher o formulário
$stmtAt = $pdo->prepare("SELECT cpf FROM atendentes WHERE id_atendente = :id");
$stmtAt->execute([':id' => $_SESSION['id_atendente']]);
$cpfAtendenteLogado = $stmtAt->fetchColumn() ?: '';

// Carrega listas auxiliares para autocomplete (<datalist>)
$clientesLista = $pdo->query("SELECT nome, cpf FROM cliente ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$veiculosDisponiveis = $pdo->query("SELECT id_veiculo, marca, modelo, placa, valor_diaria FROM veiculos WHERE status = 'Disponível' ORDER BY id_veiculo ASC")->fetchAll(PDO::FETCH_ASSOC);
$atendentesLista = $pdo->query("SELECT nome, cpf FROM atendentes WHERE status = 'Contratado' ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

$veiculoPreSelecionado = intval($_GET['id_veiculo'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Locação</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
                        <a href="#homeSubmenu" data-bs-toggle="collapse" class="nav-link active">
                            <span class="nav-link-content">
                                <i class="bi bi-house-door"></i> Home
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse show" id="homeSubmenu">
                            <ul class="submenu">
                                <li><a href="novalocacao.php" class="submenu-link active"><i
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

        <!-- Conteúdo Principal -->
        <main id="content">
            <!-- Titulo da Página -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-key-fill me-2"
                            style="color: var(--brand-orange);"></i>Abertura de Nova Locação</h3>
                </div>
                <a href="locacoes.php" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para Locações
                </a>
            </div>

            <!-- Alertas de Validação -->
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <?php
                    $erro = $_GET['erro'];
                    if ($erro === 'cliente_nao_encontrado')
                        echo 'Cliente não encontrado com o CPF informado.';
                    elseif ($erro === 'atendente_invalido')
                        echo 'Atendente não encontrado ou não está com status Contratado.';
                    elseif ($erro === 'veiculo_indisponivel')
                        echo 'Veículo não encontrado ou não está Disponível no pátio.';
                    elseif ($erro === 'datas_invalidas')
                        echo 'A data prevista de devolução deve ser posterior à data de retirada.';
                    else
                        echo 'Preencha todos os campos corretamente.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Card de Formulário -->
            <div class="form-card">
                <form action="locacao_salvar.php" method="POST">

                    <!-- Secção 1: Identificação (CPF Cliente, ID Veículo, CPF Atendente) -->
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-person-vcard me-2" style="color: var(--brand-orange);"></i>Identificação da
                        Operação
                    </h5>

                    <div class="row g-3 mb-4">
                        <!-- Input de CPF do Cliente -->
                        <div class="col-md-4">
                            <label for="cpf_cliente" class="form-label small fw-semibold text-secondary">CPF do Cliente
                                *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="cpf_cliente" name="cpf_cliente"
                                    list="listaClientes" placeholder="000.000.000-00" required>
                                <datalist id="listaClientes">
                                    <?php foreach ($clientesLista as $c): ?>
                                        <option value="<?php echo htmlspecialchars($c['cpf']); ?>">
                                            <?php echo htmlspecialchars($c['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <!-- Input de ID do Veículo -->
                        <div class="col-md-4">
                            <label for="veiculo_id" class="form-label small fw-semibold text-secondary">ID do Veículo
                                *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                <input type="number" class="form-control" id="veiculo_id" name="veiculo_id"
                                    list="listaVeiculos" placeholder="Ex: 2" min="1"
                                    value="<?php echo $veiculoPreSelecionado > 0 ? $veiculoPreSelecionado : ''; ?>"
                                    required>
                                <datalist id="listaVeiculos">
                                    <?php foreach ($veiculosDisponiveis as $v): ?>
                                        <option value="<?php echo $v['id_veiculo']; ?>">
                                            <?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ') - R$ ' . number_format($v['valor_diaria'], 2, ',', '.')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <!-- Input de CPF do Atendente -->
                        <div class="col-md-4">
                            <label for="cpf_atendente" class="form-label small fw-semibold text-secondary">CPF do
                                Atendente Responsável *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control" id="cpf_atendente" name="cpf_atendente"
                                    list="listaAtendentes" placeholder="000.000.000-00"
                                    value="<?php echo htmlspecialchars($cpfAtendenteLogado); ?>" required>
                                <datalist id="listaAtendentes">
                                    <?php foreach ($atendentesLista as $a): ?>
                                        <option value="<?php echo htmlspecialchars($a['cpf']); ?>">
                                            <?php echo htmlspecialchars($a['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- Secção 2: Datas e Condições -->
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                        <i class="bi bi-calendar-event me-2" style="color: var(--brand-orange);"></i>Período da Locação
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="data_retirada" class="form-label small fw-semibold text-secondary">Data e Hora
                                de Retirada *</label>
                            <input type="datetime-local" class="form-control" id="data_retirada" name="data_retirada"
                                value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="data_prevista_devolucao"
                                class="form-label small fw-semibold text-secondary">Data Prevista de Devolução *</label>
                            <input type="datetime-local" class="form-control" id="data_prevista_devolucao"
                                name="data_prevista_devolucao"
                                value="<?php echo date('Y-m-d\TH:i', strtotime('+3 days')); ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="observacoes" class="form-label small fw-semibold text-secondary">Observações do
                                Contrato (Opcional)</label>
                            <input type="text" class="form-control" id="observacoes" name="observacoes"
                                placeholder="Ex: Tanque cheio na retirada, cadeirinha infantil, motorista adicional...">
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="dashboard.php" class="btn btn-light px-4"
                            style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</a>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-check-circle me-1"></i> Registrar Locação
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>