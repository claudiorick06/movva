<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = trim($_POST['id_cliente'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $cnh = trim($_POST['cnh'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = strtoupper(trim($_POST['estado'] ?? ''));

    try {
        if ($id_cliente !== '') {
            $sql = "UPDATE cliente SET 
                        nome = :nome,
                        cpf = :cpf,
                        cnh = :cnh,
                        telefone = :telefone,
                        email = :email,
                        cep = :cep,
                        logradouro = :logradouro,
                        numero = :numero,
                        bairro = :bairro,
                        cidade = :cidade,
                        estado = :estado
                    WHERE id_cliente = :id_cliente";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':cnh' => $cnh,
                ':telefone' => $telefone,
                ':email' => $email,
                ':cep' => $cep,
                ':logradouro' => $logradouro,
                ':numero' => $numero,
                ':bairro' => $bairro,
                ':cidade' => $cidade,
                ':estado' => $estado,
                ':id_cliente' => $id_cliente
            ]);

            header("Location: clientes.php?msg=atualizado");
            exit;
        } else {
            $sql = "INSERT INTO cliente (nome, cpf, cnh, telefone, email, cep, logradouro, numero, bairro, cidade, estado)
                    VALUES (:nome, :cpf, :cnh, :telefone, :email, :cep, :logradouro, :numero, :bairro, :cidade, :estado)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':cnh' => $cnh,
                ':telefone' => $telefone,
                ':email' => $email,
                ':cep' => $cep,
                ':logradouro' => $logradouro,
                ':numero' => $numero,
                ':bairro' => $bairro,
                ':cidade' => $cidade,
                ':estado' => $estado
            ]);

            header("Location: clientes.php?msg=salvo");
            exit;
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: clientes.php?msg=duplicado");
            exit;
        }
        die("Erro ao salvar cliente: " . $e->getMessage());
    }
}

header("Location: clientes.php");
exit;
?>