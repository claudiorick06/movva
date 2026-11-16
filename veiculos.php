<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

$statusFiltro = trim($_GET['status'] ?? '');
$busca = trim($_GET['busca'] ?? '');

$sql = "SELECT * FROM veiculos WHERE 1=1";
$params = [];

if ($statusFiltro !== '') {
    $sql .= " AND status = :status";
    $params[':status'] = $statusFiltro;
}

if ($busca !== '') {
    $sql .= " AND (marca LIKE :busca OR modelo LIKE :busca OR placa LIKE :busca OR categoria LIKE :busca)";
    $params[':busca'] = "%$busca%";
}

$sql .= ($statusFiltro === 'Disponível')
    ? " ORDER BY categoria ASC, valor_diaria ASC"
    : " ORDER BY id_veiculo DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define título e subtítulo dinâmicos conforme o filtro ativo
$tituloPagina = 'Gestão de Frota';
$iconeTitulo = 'bi-car-front-fill';
$corIcone = 'color: var(--brand-orange);';

if ($statusFiltro === 'Disponível') {
    $tituloPagina = 'Veículos Disponíveis';
    $subtituloPagina = 'Veículos prontos no pátio para abertura imediata de contrato.';
    $iconeTitulo = 'bi-check-circle-fill text-success';
    $corIcone = '';
} elseif ($statusFiltro === 'Alugado') {
    $tituloPagina = 'Veículos Alugados';
    $subtituloPagina = 'Veículos atualmente em operação com contratos ativos.';
    $iconeTitulo = 'bi-key-fill text-warning';
    $corIcone = '';
} elseif ($statusFiltro === 'Em manutenção') {
    $tituloPagina = 'Veículos em Manutenção';
    $subtituloPagina = 'Veículos indisponíveis temporariamente para revisão ou reparo.';
    $iconeTitulo = 'bi-wrench-adjustable-circle-fill text-danger';
    $corIcone = '';
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tituloPagina); ?></title>
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
                    <img src="./images/logo.png" alt="Movvi Logo" class="brand-logo-img">
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
                        <a href="#veiculosSubmenu" data-bs-toggle="collapse" class="nav-link active">
                            <span class="nav-link-content">
                                <i class="bi bi-car-front"></i> Veículos
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse show" id="veiculosSubmenu">
                            <ul class="submenu">
                                <li><a href="veiculos.php"
                                        class="submenu-link <?php echo $statusFiltro === '' ? 'active' : ''; ?>">Frota
                                        Completa</a></li>
                                <li><a href="veiculos.php?status=Disponível"
                                        class="submenu-link <?php echo $statusFiltro === 'Disponível' ? 'active' : ''; ?>">Disponíveis</a>
                                </li>
                                <li><a href="veiculos.php?status=Alugado"
                                        class="submenu-link <?php echo $statusFiltro === 'Alugado' ? 'active' : ''; ?>">Alugados</a>
                                </li>
                                <li><a href="veiculos.php?status=Em manutenção"
                                        class="submenu-link <?php echo $statusFiltro === 'Em manutenção' ? 'active' : ''; ?>">Em
                                        Manutenção</a></li>
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
            <!-- Cabeçalho -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi <?php echo $iconeTitulo; ?> me-2"
                            style="<?php echo $corIcone; ?>"></i><?php echo htmlspecialchars($tituloPagina); ?>
                    </h3>
                    <p class="text-secondary small mb-0"><?php echo htmlspecialchars($subtituloPagina); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <form action="veiculos.php" method="GET" class="d-flex gap-2">
                        <?php if ($statusFiltro !== ''): ?>
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFiltro); ?>">
                        <?php endif; ?>
                        <input type="text" name="busca" class="form-control" placeholder="Modelo, placa, categoria..."
                            value="<?php echo htmlspecialchars($busca); ?>" style="border-radius: 10px;">
                        <button type="submit" class="btn btn-outline-secondary" style="border-radius: 10px;">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($busca !== ''): ?>
                            <a href="veiculos.php<?php echo $statusFiltro !== '' ? '?status=' . urlencode($statusFiltro) : ''; ?>"
                                class="btn btn-light border" style="border-radius: 10px;" title="Limpar busca">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="btn btn-orange" data-bs-toggle="modal"
                        data-bs-target="#modalNovoVeiculo">
                        <i class="bi bi-plus-lg me-1"></i> Novo Veículo
                    </button>
                </div>
            </div>

            <!-- Alertas -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'salvo'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Veículo cadastrado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'atualizado'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> Dados do veículo atualizados!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'excluido'): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-trash-fill me-1"></i> Veículo removido da frota.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'duplicado'): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Erro: já existe um veículo cadastrado com esta
                        placa.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] === 'vinculado'): ?>
                    <div class="alert alert-warning alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-shield-exclamation me-1"></i> Não é possível excluir: este veículo possui contratos de
                        locação vinculados.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Barra de Filtro Rápido -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="btn-group" role="group">
                    <a href="veiculos.php"
                        class="btn btn-sm <?php echo $statusFiltro === '' ? 'btn-dark' : 'btn-outline-secondary'; ?>">Todos</a>
                    <a href="veiculos.php?status=Disponível"
                        class="btn btn-sm <?php echo $statusFiltro === 'Disponível' ? 'btn-success' : 'btn-outline-secondary'; ?>">Disponíveis</a>
                    <a href="veiculos.php?status=Alugado"
                        class="btn btn-sm <?php echo $statusFiltro === 'Alugado' ? 'btn-warning' : 'btn-outline-secondary'; ?>">Alugados</a>
                    <a href="veiculos.php?status=Em manutenção"
                        class="btn btn-sm <?php echo $statusFiltro === 'Em manutenção' ? 'btn-danger' : 'btn-outline-secondary'; ?>">Em
                        Manutenção</a>
                </div>
                <span class="text-secondary small">Exibindo <strong><?php echo count($veiculos); ?></strong>
                    veículos</span>
            </div>

            <!-- Tabela de Veículos -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Modelo / Marca</th>
                                <th>Placa</th>
                                <th>Ano</th>
                                <th>Categoria</th>
                                <th>Valor Diária</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($veiculos) > 0): ?>
                                <?php foreach ($veiculos as $v): ?>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    if ($v['status'] === 'Disponível')
                                        $badgeClass = 'bg-success';
                                    elseif ($v['status'] === 'Alugado')
                                        $badgeClass = 'bg-warning text-dark';
                                    elseif ($v['status'] === 'Em manutenção')
                                        $badgeClass = 'bg-danger';
                                    ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $v['id_veiculo']; ?></td>
                                        <td>
                                            <div class="fw-semibold">
                                                <?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?></div>
                                            <small class="text-secondary">Cor:
                                                <?php echo htmlspecialchars($v['cor']); ?></small>
                                        </td>
                                        <td><span
                                                class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($v['placa']); ?></span>
                                        </td>
                                        <td><?php echo $v['ano']; ?></td>
                                        <td><?php echo htmlspecialchars($v['categoria']); ?></td>
                                        <td class="fw-semibold">R$ <?php echo number_format($v['valor_diaria'], 2, ',', '.'); ?>
                                        </td>
                                        <td><span
                                                class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($v['status']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($v['status'] === 'Disponível'): ?>
                                                <a href="novalocacao.php?id_veiculo=<?php echo $v['id_veiculo']; ?>"
                                                    class="btn btn-sm btn-orange me-1" title="Alugar este veículo">
                                                    <i class="bi bi-key me-1"></i> Alugar
                                                </a>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Editar veículo"
                                                onclick='abrirModalEditarVeiculo(<?php echo json_encode($v, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="veiculo_excluir.php?id=<?php echo $v['id_veiculo']; ?>"
                                                class="btn btn-sm btn-outline-danger" title="Excluir veículo"
                                                onclick="return confirm('Tem certeza que deseja excluir o veículo <?php echo htmlspecialchars(addslashes($v['modelo'] . ' - ' . $v['placa'])); ?>?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-secondary">
                                        <i class="bi bi-car-front fs-2 d-block mb-2 text-muted"></i>
                                        Nenhum veículo encontrado para este filtro.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Novo Veículo -->
    <div class="modal fade" id="modalNovoVeiculo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-car-front-fill me-2" style="color: var(--brand-orange);"></i>Cadastrar Novo
                        Veículo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="veiculo_salvar.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Marca *</label>
                                <input type="text" class="form-control" name="marca" placeholder="Ex: Fiat, Volkswagen"
                                    required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Modelo *</label>
                                <input type="text" class="form-control" name="modelo" placeholder="Ex: Mobi Like 1.0"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Ano *</label>
                                <input type="number" class="form-control" name="ano" min="1990" max="2030" value="2025"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Placa *</label>
                                <input type="text" class="form-control text-uppercase" name="placa"
                                    placeholder="ABC1D23" maxlength="8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Cor *</label>
                                <input type="text" class="form-control" name="cor" placeholder="Ex: Prata, Preto"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Categoria *</label>
                                <select class="form-select" name="categoria" required>
                                    <option value="Econômico">Econômico</option>
                                    <option value="Hatch">Hatch</option>
                                    <option value="Sedan">Sedan</option>
                                    <option value="SUV">SUV</option>
                                    <option value="Utilitário">Utilitário</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Valor da Diária (R$)
                                    *</label>
                                <input type="number" step="0.01" min="1" class="form-control" name="valor_diaria"
                                    placeholder="120.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Status Inicial *</label>
                                <select class="form-select" name="status" required>
                                    <option value="Disponível" selected>Disponível</option>
                                    <option value="Em manutenção">Em manutenção</option>
                                    <option value="Alugado">Alugado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</button>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-check-circle me-1"></i> Salvar Veículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Veículo -->
    <div class="modal fade" id="modalEditarVeiculo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2" style="color: var(--brand-orange);"></i>Editar Veículo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="veiculo_salvar.php" method="POST">
                    <input type="hidden" name="id_veiculo" id="edit_id_veiculo">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Marca *</label>
                                <input type="text" class="form-control" id="edit_marca" name="marca" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Modelo *</label>
                                <input type="text" class="form-control" id="edit_modelo" name="modelo" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Ano *</label>
                                <input type="number" class="form-control" id="edit_ano" name="ano" min="1990" max="2030"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Placa *</label>
                                <input type="text" class="form-control text-uppercase" id="edit_placa" name="placa"
                                    maxlength="8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Cor *</label>
                                <input type="text" class="form-control" id="edit_cor" name="cor" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Categoria *</label>
                                <select class="form-select" id="edit_categoria" name="categoria" required>
                                    <option value="Econômico">Econômico</option>
                                    <option value="Hatch">Hatch</option>
                                    <option value="Sedan">Sedan</option>
                                    <option value="SUV">SUV</option>
                                    <option value="Utilitário">Utilitário</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Valor da Diária (R$)
                                    *</label>
                                <input type="number" step="0.01" min="1" class="form-control" id="edit_valor_diaria"
                                    name="valor_diaria" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Status *</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="Disponível">Disponível</option>
                                    <option value="Alugado">Alugado</option>
                                    <option value="Em manutenção">Em manutenção</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; border: 1px solid var(--card-border);">Cancelar</button>
                        <button type="submit" class="btn btn-orange">
                            <i class="bi bi-check-circle me-1"></i> Atualizar Veículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModalEditarVeiculo(veiculo) {
            document.getElementById('edit_id_veiculo').value = veiculo.id_veiculo;
            document.getElementById('edit_marca').value = veiculo.marca;
            document.getElementById('edit_modelo').value = veiculo.modelo;
            document.getElementById('edit_ano').value = veiculo.ano;
            document.getElementById('edit_placa').value = veiculo.placa;
            document.getElementById('edit_cor').value = veiculo.cor;
            document.getElementById('edit_categoria').value = veiculo.categoria;
            document.getElementById('edit_valor_diaria').value = veiculo.valor_diaria;
            document.getElementById('edit_status').value = veiculo.status;

            const modal = new bootstrap.Modal(document.getElementById('modalEditarVeiculo'));
            modal.show();
        }
    </script>
</body>

</html>