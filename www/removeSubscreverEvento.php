<?php

include(".htconfig.php");

$idPessoa = $_SESSION["idPessoa"];
$idEvento = $_GET["idEvento"];

$stmtRemove = $mysql->prepare("DELETE FROM PessoaEvento WHERE idPessoa=? AND idEvento=?;");
$stmtRemove->bind_param("ii", $idPessoa, $idEvento);

if ($stmtRemove->execute()) {
    header("location: ./evento.php?idEvento=".$idEvento);
} else {
    echo "Erro: (" . $stmtRemove->errno . ") " . $stmtRemove->error;
}

$stmtRemove->close();
$mysql->close();
?>
