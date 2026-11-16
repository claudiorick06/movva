<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.html");
    exit;
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_veiculo   = trim($_POST['id_veiculo'] ?? '');
    $marca        = trim($_POST['marca'] ?? '');
    $modelo       = trim($_POST['modelo'] ?? '');
    $placa        = strtoupper(trim($_POST['placa'] ?? ''));
    $ano          = intval($_POST['ano'] ?? 0);
    $cor          = trim($_POST['cor'] ?? '');
    $categoria    = trim($_POST['categoria'] ?? '');
    $valor_diaria = floatval($_POST['valor_diaria'] ?? 0);
    $status       = trim($_POST['status'] ?? 'Disponível');

    try {
        if ($id_veiculo !== '') {
            $sql = "UPDATE veiculos SET 
                        marca = :marca,
                        modelo = :modelo,
                        placa = :placa,
                        ano = :ano,
                        cor = :cor,
                        categoria = :categoria,
                        valor_diaria = :valor_diaria,
                        status = :status
                    WHERE id_veiculo = :id_veiculo";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':marca'        => $marca,
                ':modelo'       => $modelo,
                ':placa'        => $placa,
                ':ano'          => $ano,
                ':cor'          => $cor,
                ':categoria'    => $categoria,
                ':valor_diaria' => $valor_diaria,
                ':status'       => $status,
                ':id_veiculo'   => $id_veiculo
            ]);

            header("Location: veiculos.php?msg=atualizado");
            exit;
        } else {
            $sql = "INSERT INTO veiculos (marca, modelo, placa, ano, cor, categoria, valor_diaria, status)
                    VALUES (:marca, :modelo, :placa, :ano, :cor, :categoria, :valor_diaria, :status)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':marca'        => $marca,
                ':modelo'       => $modelo,
                ':placa'        => $placa,
                ':ano'          => $ano,
                ':cor'          => $cor,
                ':categoria'    => $categoria,
                ':valor_diaria' => $valor_diaria,
                ':status'       => $status
            ]);

            header("Location: veiculos.php?msg=salvo");
            exit;
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: veiculos.php?msg=duplicado");
            exit;
        }
        die("Erro ao salvar veículo: " . $e->getMessage());
    }
}

header("Location: veiculos.php");
exit;
?>