<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

$statusFiltro = trim($_GET['status'] ?? '');
$idDestaque = intval($_GET['id'] ?? 0);

$sql = "SELECT 
            l.*,
            c.nome AS nome_cliente,
            c.cpf AS cpf_cliente,
            v.marca,
            v.modelo,
            v.placa,
            a.nome AS nome_atendente,
            a.cpf AS cpf_atendente
        FROM locacoes l
        INNER JOIN cliente c ON l.id_cliente = c.id_cliente
        INNER JOIN veiculos v ON l.id_veiculo = v.id_veiculo
        INNER JOIN atendentes a ON l.id_atendente = a.id_atendente
        WHERE 1=1";

$params = [];

if ($statusFiltro !== '') {
    $sql .= " AND l.status = :status";
    $params[':status'] = $statusFiltro;
}

$sql .= " ORDER BY l.id_locacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$locacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locações</title>
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
                                <li><a href="novalocacao.php" class="submenu-link"><i
                                            class="bi bi-plus-circle me-1"></i> Nova Locação</a></li>
                                <li><a href="locacoes.php" class="submenu-link active"><i
                                            class="bi bi-file-text me-1"></i> Locações</a></li>
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
                        <ul class="submenu">
                            <li><a href="veiculos.php" class="submenu-link">Frota Completa</a></li>
                            <li><a href="veiculos.php?status=Disponível" class="submenu-link">Disponíveis</a></li>
                            <li><a href="veiculos.php?status=Alugado" class="submenu-link">Alugados</a></li>
                            <li><a href="veiculos.php?status=Em manutenção" class="submenu-link">Em Manutenção</a></li>
                        </ul>
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
            <!-- Título e Ação -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-file-text-fill me-2"
                            style="color: var(--brand-orange);"></i>Gestão de Locações</h3>
                </div>
                <a href="novalocacao.php" class="btn btn-orange">
                    <i class="bi bi-plus-lg me-1"></i> Nova Locação
                </a>
            </div>

            <!-- Alertas -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'criada'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Contrato de locação registrado com sucesso! Veículo marcado
                        como Alugado.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'finalizada'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check2-all me-1"></i> Devolução registrada! Veículo liberado novamente para o pátio.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'cancelada'): ?>
                    <div class="alert alert-warning alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-x-circle-fill me-1"></i> Contrato cancelado e veículo liberado.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Filtros de Status -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="btn-group" role="group">
                    <a href="locacoes.php"
                        class="btn btn-sm <?php echo $statusFiltro === '' ? 'btn-dark' : 'btn-outline-secondary'; ?>">Todas</a>
                    <a href="locacoes.php?status=Ativa"
                        class="btn btn-sm <?php echo $statusFiltro === 'Ativa' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Ativas</a>
                    <a href="locacoes.php?status=Finalizada"
                        class="btn btn-sm <?php echo $statusFiltro === 'Finalizada' ? 'btn-success' : 'btn-outline-secondary'; ?>">Finalizadas</a>
                    <a href="locacoes.php?status=Cancelada"
                        class="btn btn-sm <?php echo $statusFiltro === 'Cancelada' ? 'btn-danger' : 'btn-outline-secondary'; ?>">Canceladas</a>
                </div>
                <span class="text-secondary small">Exibindo <strong><?php echo count($locacoes); ?></strong>
                    contratos</span>
            </div>

            <!-- Tabela de Contratos -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID Contrato</th>
                                <th>CPF Cliente</th>
                                <th>ID Veículo</th>
                                <th>CPF Atendente</th>
                                <th>Data Retirada</th>
                                <th>Prev. Devolução</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($locacoes) > 0): ?>
                                <?php foreach ($locacoes as $loc): ?>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    if ($loc['status'] === 'Ativa')
                                        $badgeClass = 'bg-primary';
                                    elseif ($loc['status'] === 'Finalizada')
                                        $badgeClass = 'bg-success';
                                    elseif ($loc['status'] === 'Cancelada')
                                        $badgeClass = 'bg-danger';

                                    $rowHighlight = ($idDestaque === intval($loc['id_locacao'])) ? 'table-warning' : '';
                                    ?>
                                    <tr class="<?php echo $rowHighlight; ?>">
                                        <td class="fw-bold">#<?php echo str_pad($loc['id_locacao'], 4, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($loc['cpf_cliente']); ?></div>
                                            <small
                                                class="text-secondary"><?php echo htmlspecialchars($loc['nome_cliente']); ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">#<?php echo $loc['id_veiculo']; ?> -
                                                <?php echo htmlspecialchars($loc['modelo']); ?>
                                            </div>
                                            <small
                                                class="text-secondary font-monospace"><?php echo htmlspecialchars($loc['placa']); ?></small>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($loc['cpf_atendente']); ?></div>
                                            <small
                                                class="text-secondary"><?php echo htmlspecialchars($loc['nome_atendente']); ?></small>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($loc['data_retirada'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($loc['data_limite_devolucao'])); ?></td>
                                        <td class="fw-semibold">R$
                                            <?php echo number_format($loc['valor_total'], 2, ',', '.'); ?>
                                        </td>
                                        <td><span
                                                class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($loc['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($loc['status'] === 'Ativa'): ?>
                                                <a href="locacao_status.php?id=<?php echo $loc['id_locacao']; ?>&acao=finalizar"
                                                    class="btn btn-sm btn-outline-success" title="Registrar devolução"
                                                    onclick="return confirm('Confirmar devolução do veículo e finalizar o contrato #<?php echo $loc['id_locacao']; ?>?');">
                                                    <i class="bi bi-check-lg"></i>
                                                </a>
                                                <a href="locacao_status.php?id=<?php echo $loc['id_locacao']; ?>&acao=cancelar"
                                                    class="btn btn-sm btn-outline-danger" title="Cancelar contrato"
                                                    onclick="return confirm('Tem certeza que deseja cancelar o contrato #<?php echo $loc['id_locacao']; ?>?');">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-secondary">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                        Nenhuma locação cadastrada no banco de dados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>