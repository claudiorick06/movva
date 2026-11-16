<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_atendente = trim($_POST['id_atendente'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cargo = trim($_POST['cargo'] ?? 'Atendente');
    $data_admissao = trim($_POST['data_admissao'] ?? date('Y-m-d'));
    $status = trim($_POST['status'] ?? 'Contratado');
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    try {
        if ($id_atendente !== '') {
            $sql = "UPDATE atendentes SET 
                        nome = :nome,
                        cpf = :cpf,
                        telefone = :telefone,
                        email = :email,
                        cargo = :cargo,
                        data_admissao = :data_admissao,
                        status = :status,
                        login = :login,
                        senha = :senha
                    WHERE id_atendente = :id_atendente";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':telefone' => $telefone,
                ':email' => $email,
                ':cargo' => $cargo,
                ':data_admissao' => $data_admissao,
                ':status' => $status,
                ':login' => $login,
                ':senha' => $senha,
                ':id_atendente' => $id_atendente
            ]);

            header("Location: atendentes.php?msg=atualizado");
            exit;
        } else {
            $sql = "INSERT INTO atendentes (nome, cpf, telefone, email, cargo, data_admissao, status, login, senha)
                    VALUES (:nome, :cpf, :telefone, :email, :cargo, :data_admissao, :status, :login, :senha)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':telefone' => $telefone,
                ':email' => $email,
                ':cargo' => $cargo,
                ':data_admissao' => $data_admissao,
                ':status' => $status,
                ':login' => $login,
                ':senha' => $senha
            ]);

            header("Location: atendentes.php?msg=salvo");
            exit;
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: atendentes.php?msg=duplicado");
            exit;
        }
        die("Erro ao salvar colaborador: " . $e->getMessage());
    }
}

header("Location: atendentes.php");
exit;
?>