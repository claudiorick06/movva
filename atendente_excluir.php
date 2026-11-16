<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

$id = intval($_GET['id'] ?? 0);

if ($id === 1) {
    header("Location: atendentes.php?msg=admin_protegido");
    exit;
}

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM atendentes WHERE id_atendente = :id");
        $stmt->execute([':id' => $id]);

        header("Location: atendentes.php?msg=excluido");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: atendentes.php?msg=vinculado");
            exit;
        }
        die("Erro ao excluir colaborador: " . $e->getMessage());
    }
}

header("Location: atendentes.php");
exit;
?>