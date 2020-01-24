<?php
include(".htconfig.php");

$idPessoa = $_GET['idPessoa'];

$stmt = $mysql->prepare("UPDATE Pessoa SET ativo = 1 WHERE idPessoa = ?");
$stmt->bind_param("i", $idPessoa);

// execute prepared statement
if ($stmt->execute()) {
    header("location: ./index.php");
} else {
    echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->close();
?>
