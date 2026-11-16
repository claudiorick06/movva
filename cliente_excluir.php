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
        $stmt = $pdo->prepare("DELETE FROM cliente WHERE id_cliente = :id");
        $stmt->execute([':id' => $id]);

        header("Location: clientes.php?msg=excluido");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: clientes.php?msg=vinculado");
            exit;
        }
        die("Erro ao excluir cliente: " . $e->getMessage());
    }
}

header("Location: clientes.php");
exit;
?>