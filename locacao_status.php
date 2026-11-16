<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

$id   = intval($_GET['id'] ?? 0);
$acao = trim($_GET['acao'] ?? '');

if ($id > 0 && ($acao === 'finalizar' || $acao === 'cancelar')) {
    $stmt = $pdo->prepare("SELECT id_locacao, id_veiculo, status FROM locacoes WHERE id_locacao = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $locacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($locacao && $locacao['status'] === 'Ativa') {
        try {
            $pdo->beginTransaction();

            if ($acao === 'finalizar') {
                $stmtLoc = $pdo->prepare("UPDATE locacoes SET status = 'Finalizada', data_devolucao_real = NOW() WHERE id_locacao = :id");
                $stmtLoc->execute([':id' => $id]);
                $msg = 'finalizada';
            } else {
                $stmtLoc = $pdo->prepare("UPDATE locacoes SET status = 'Cancelada' WHERE id_locacao = :id");
                $stmtLoc->execute([':id' => $id]);
                $msg = 'cancelada';
            }

            // Libera o veículo novamente para o pátio
            $stmtVei = $pdo->prepare("UPDATE veiculos SET status = 'Disponível' WHERE id_veiculo = :id_veiculo");
            $stmtVei->execute([':id_veiculo' => $locacao['id_veiculo']]);

            $pdo->commit();

            header("Location: locacoes.php?msg=" . $msg);
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            die("Erro ao atualizar status da locação: " . $e->getMessage());
        }
    }
}

header("Location: locacoes.php");
exit;
?>