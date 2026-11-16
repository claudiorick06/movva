<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($login === '' || $senha === '') {
        header("Location: index.html?erro=campos_vazios");
        exit;
    }

    $stmt = $pdo->prepare("SELECT id_atendente, nome, cargo, status FROM atendentes WHERE login = :login AND senha = :senha LIMIT 1");
    $stmt->execute([
        ':login' => $login,
        ':senha' => $senha
    ]);

    $atendente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($atendente) {
        if ($atendente['status'] !== 'Contratado') {
            header("Location: index.html?erro=inativo");
            exit;
        }

        $_SESSION['id_atendente'] = $atendente['id_atendente'];
        $_SESSION['nome_atendente'] = $atendente['nome'];
        $_SESSION['cargo_atendente'] = $atendente['cargo'];

        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: index.html?erro=invalido");
        exit;
    }
} else {
    header("Location: index.html");
    exit;
}
?>