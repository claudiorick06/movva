<?php
$host = 'localhost';
$banco = 'locadora';

//! Mude para o usuario e senha do banco da sua maquina

$usuario = 'root';
$senha = '7742';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>