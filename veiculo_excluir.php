<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM veiculos WHERE id_veiculo = :id");
        $stmt->execute([':id' => $id]);

        header("Location: veiculos.php?msg=excluido");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: veiculos.php?msg=vinculado");
            exit;
        }
        die("Erro ao excluir veículo: " . $e->getMessage());
    }
}

header("Location: veiculos.php");
exit;
?>