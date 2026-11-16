<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

// Contadores para os cards superiores
$totalDisponiveis = $pdo->query("SELECT COUNT(*) FROM veiculos WHERE status = 'Disponível'")->fetchColumn();
$totalLocacoesAtivas = $pdo->query("SELECT COUNT(*) FROM locacoes WHERE status = 'Ativa'")->fetchColumn();
$totalClientes = $pdo->query("SELECT COUNT(*) FROM cliente")->fetchColumn();
$totalColaboradores = $pdo->query("SELECT COUNT(*) FROM atendentes")->fetchColumn();

// Consulta das últimas 10 locações com JOIN nas 3 tabelas relacionadas
$sqlUltimas = "SELECT 
                l.id_locacao,
                c.nome AS nome_cliente,
                v.marca,
                v.modelo,
                v.placa,
                a.nome AS nome_atendente,
                l.data_retirada,
                l.status
               FROM locacoes l
               INNER JOIN cliente c ON l.id_cliente = c.id_cliente
               INNER JOIN veiculos v ON l.id_veiculo = v.id_veiculo
               INNER JOIN atendentes a ON l.id_atendente = a.id_atendente
               ORDER BY l.data_retirada DESC
               LIMIT 10";

$ultimasLocacoes = $pdo->query($sqlUltimas)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="painelstyle.css">
    <link rel="icon" href="images/logo-fav.png" type="image/png">
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar / Painel Lateral -->
        <nav id="sidebar">
            <div>
                <!-- Logo Header -->
                <div class="sidebar-header text-center">
                    <img src="./images/logo.png" alt="Movva Logo" class="brand-logo-img">
                </div>

                <!-- Navegação -->
                <ul class="sidebar-menu">
                    <li class="menu-title">Menu Principal</li>

                    <!-- Home (Com Submenu) -->
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
                                <li><a href="locacoes.php" class="submenu-link"><i class="bi bi-file-text me-1"></i>
                                        Locações</a></li>
                                <li><a href="dashboard.php#estatisticas" class="submenu-link"><i
                                            class="bi bi-graph-up me-1"></i>
                                        Estatísticas</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Veículos (Com Submenu) -->
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

                    <!-- Clientes -->
                    <li class="mt-2">
                        <a href="clientes.php" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-people"></i> Clientes
                            </span>
                        </a>
                    </li>

                    <!-- Colaboradores -->
                    <li class="mt-2">
                        <a href="atendentes.php" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-person-badge"></i> Colaboradores
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Rodapé da Sidebar -->
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
            <!-- Banner de Boas-Vindas -->
            <div class="welcome-card">
                <div>
                    <h1 class="display-5 fw-bold mb-0">BEM-VINDO,
                        <?php echo htmlspecialchars(strtoupper($_SESSION['nome_atendente'])); ?>!
                    </h1>
                </div>

                <!-- Canto Inferior Esquerdo: Relógio e Data -->
                <div class="d-flex align-items-baseline gap-3 mt-4">
                    <span id="live-clock" class="fs-4 fw-bold text-dark font-monospace">00:00:00</span>
                    <span id="live-date" class="text-secondary small">00/00/0000</span>
                </div>
            </div>

            <!-- Indicadores / Cards de Métricas -->
            <div id="estatisticas" class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">FROTA DISPONÍVEL</span>
                            <h3 class="fw-bold mb-0 mt-1"><?php echo $totalDisponiveis; ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">LOCAÇÕES ATIVAS</span>
                            <h3 class="fw-bold mb-0 mt-1"><?php echo $totalLocacoesAtivas; ?></h3>
                        </div>
                        <div class="stat-icon" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                            <i class="bi bi-key-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">TOTAL CLIENTES</span>
                            <h3 class="fw-bold mb-0 mt-1"><?php echo $totalClientes; ?></h3>
                        </div>
                        <div class="stat-icon" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small fw-semibold">COLABORADORES</span>
                            <h3 class="fw-bold mb-0 mt-1"><?php echo $totalColaboradores; ?></h3>
                        </div>
                        <div class="stat-icon" style="background-color: rgba(108, 117, 125, 0.1); color: #6c757d;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Acessos Rápidos / Últimas Locações -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"
                    style="border-radius: 16px 16px 0 0;">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"
                            style="color: var(--brand-orange);"></i>Últimas Locações Registradas</h5>
                    <a href="locacoes.php" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">Ver
                        Todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#Contrato</th>
                                    <th>Cliente</th>
                                    <th>Veículo</th>
                                    <th>Atendente</th>
                                    <th>Data Retirada</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($ultimasLocacoes) > 0): ?>
                                    <?php foreach ($ultimasLocacoes as $loc): ?>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($loc['status'] === 'Ativa')
                                            $badgeClass = 'bg-primary';
                                        elseif ($loc['status'] === 'Finalizada')
                                            $badgeClass = 'bg-success';
                                        elseif ($loc['status'] === 'Cancelada')
                                            $badgeClass = 'bg-danger';
                                        ?>
                                        <tr>
                                            <td class="fw-bold">
                                                #<?php echo str_pad($loc['id_locacao'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo htmlspecialchars($loc['nome_cliente']); ?></td>
                                            <td><?php echo htmlspecialchars($loc['marca'] . ' ' . $loc['modelo']); ?> <span
                                                    class="badge bg-light text-dark border"><?php echo htmlspecialchars($loc['placa']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($loc['nome_atendente']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($loc['data_retirada'])); ?></td>
                                            <td><span
                                                    class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($loc['status']); ?></span>
                                            </td>
                                            <td class="text-end">
                                                <a href="locacoes.php?id=<?php echo $loc['id_locacao']; ?>"
                                                    class="btn btn-sm btn-outline-secondary" title="Ver na lista">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-secondary">
                                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                            Nenhuma locação registrada até o momento.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de Relógio em Tempo Real -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();

            document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;
            document.getElementById('live-date').textContent = `${day}/${month}/${year}`;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>

</body>

</html>