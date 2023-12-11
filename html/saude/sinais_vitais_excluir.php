<?php

session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../../index.php");
}

// Verifica Permissão do Usuário
require_once '../permissao/permissao.php';
permissao($_SESSION['id_pessoa'], 11, 7);

require_once "../../dao/Conexao.php";
$_POST["id_sinais_vitais"];

try {
    $pdo = Conexao::connect();

    $stmt = $pdo->prepare("DELETE FROM sinais_vitais WHERE id_sinais_vitais = :id_sinais_vitais");

    $stmt->bindParam(':id_sinais_vitais', $sin_vit, PDO::PARAM_INT);

    $stmt->execute();

    $linhas_afetadas = $stmt->rowCount();
    if ($linhas_afetadas > 0) {
        echo json_encode(array("mensagem" => "Exclusão bem-sucedida"));
    } else {
        echo json_encode(array("mensagem" => "Nenhum registro encontrado para exclusão"));
    }
} catch (PDOException $e) {
    echo json_encode(array("erro" => "Ocorreu um erro ao excluir os sinais vitais: ". $e));
} finally {
    $pdo = null;
}