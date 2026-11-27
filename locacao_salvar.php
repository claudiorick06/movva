<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf_cliente             = trim($_POST['cpf_cliente'] ?? '');
    $veiculo_id              = intval($_POST['veiculo_id'] ?? 0);
    $cpf_atendente           = trim($_POST['cpf_atendente'] ?? '');
    $data_retirada           = trim($_POST['data_retirada'] ?? '');
    $data_prevista_devolucao = trim($_POST['data_prevista_devolucao'] ?? '');
    $observacoes             = trim($_POST['observacoes'] ?? '');

    if ($cpf_cliente === '' || $veiculo_id <= 0 || $cpf_atendente === '' || $data_retirada === '' || $data_prevista_devolucao === '') {
        header("Location: novalocacao.php?erro=campos");
        exit;
    }

    $tsRetirada = strtotime($data_retirada);
    $tsDevolucao = strtotime($data_prevista_devolucao);

    if ($tsDevolucao <= $tsRetirada) {
        header("Location: novalocacao.php?erro=datas_invalidas");
        exit;
    }

    //Verifica Cliente pelo CPF
    $stmtCli = $pdo->prepare("SELECT id_cliente FROM cliente WHERE cpf = :cpf LIMIT 1");
    $stmtCli->execute([':cpf' => $cpf_cliente]);
    $cliente = $stmtCli->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        header("Location: novalocacao.php?erro=cliente_nao_encontrado");
        exit;
    }

    //Verifica Atendente pelo CPF e se está Contratado
    $stmtAt = $pdo->prepare("SELECT id_atendente FROM atendentes WHERE cpf = :cpf AND status = 'Contratado' LIMIT 1");
    $stmtAt->execute([':cpf' => $cpf_atendente]);
    $atendente = $stmtAt->fetch(PDO::FETCH_ASSOC);

    if (!$atendente) {
        header("Location: novalocacao.php?erro=atendente_invalido");
        exit;
    }

    //Verifica Veículo pelo ID e se está Disponível
    $stmtVei = $pdo->prepare("SELECT id_veiculo, valor_diaria, status FROM veiculos WHERE id_veiculo = :id LIMIT 1");
    $stmtVei->execute([':id' => $veiculo_id]);
    $veiculo = $stmtVei->fetch(PDO::FETCH_ASSOC);

    if (!$veiculo || $veiculo['status'] !== 'Disponível') {
        header("Location: novalocacao.php?erro=veiculo_indisponivel");
        exit;
    }

    // Calcula quantidade de diárias (mínimo 1 diária)
    $dias = max(1, ceil(($tsDevolucao - $tsRetirada) / 86400));
    $valor_total = $dias * floatval($veiculo['valor_diaria']);

    $dtRetiradaSql = date('Y-m-d H:i:s', $tsRetirada);
    $dtLimiteSql   = date('Y-m-d H:i:s', $tsDevolucao);

    try {
        $pdo->beginTransaction();

        $sqlInsert = "INSERT INTO locacoes (id_cliente, id_veiculo, id_atendente, data_retirada, data_limite_devolucao, valor_total, status, observacoes)
                      VALUES (:id_cliente, :id_veiculo, :id_atendente, :data_retirada, :data_limite_devolucao, :valor_total, 'Ativa', :observacoes)";

        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            ':id_cliente'            => $cliente['id_cliente'],
            ':id_veiculo'            => $veiculo['id_veiculo'],
            ':id_atendente'          => $atendente['id_atendente'],
            ':data_retirada'         => $dtRetiradaSql,
            ':data_limite_devolucao' => $dtLimiteSql,
            ':valor_total'           => $valor_total,
            ':observacoes'           => ($observacoes !== '' ? $observacoes : null)
        ]);

        // Atualiza status do veículo para 'Alugado'
        $stmtUpdateVei = $pdo->prepare("UPDATE veiculos SET status = 'Alugado' WHERE id_veiculo = :id");
        $stmtUpdateVei->execute([':id' => $veiculo['id_veiculo']]);

        $pdo->commit();

        header("Location: locacoes.php?msg=criada");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erro ao registrar locação: " . $e->getMessage());
    }
}

header("Location: novalocacao.php");
exit;
?>