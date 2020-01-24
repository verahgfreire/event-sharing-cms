<?php

include(".htconfig.php");
require_once("sendMail.php");

$idPessoa = $_SESSION["idPessoa"];
$idEvento = $_GET["idEvento"];

$stmtAdd = $mysql->prepare("INSERT INTO PessoaEvento (idPessoa, idEvento) VALUES (?,?);");
$stmtAdd->bind_param("ii", $idPessoa, $idEvento);

if ($stmtAdd->execute()) {

    $stmtEmailPessoa = $mysql->prepare("SELECT email FROM Pessoa WHERE idPessoa=?");
    $stmtEmailPessoa->bind_param("i", $idPessoa);

    if ($stmtEmailPessoa->execute()) {
        // bind result variables
        $stmtEmailPessoa->bind_result($emailPessoa);

        // fetch value
        $stmtEmailPessoa->fetch();

    } else {
        echo "Erro: (" . $stmtEmailPessoa->errno . ") " . $stmtEmailPessoa->error;
    }

    $stmtEmailPessoa->close();
    
    $stmtNomeEvento = $mysql->prepare("SELECT nomeEvento FROM Evento WHERE idEvento=?");
    $stmtNomeEvento->bind_param("i", $idEvento);

    if ($stmtNomeEvento->execute()) {
        // bind result variables
        $stmtNomeEvento->bind_result($nomeEvento);

        // fetch value
        $stmtNomeEvento->fetch();

    } else {
        echo "Erro: (" . $stmtNomeEvento->errno . ") " . $stmtNomeEvento->error;
    }

    $stmtNomeEvento->close();
    
    $subject = "Inscricão com Sucesso no Evento";
    $message = "Bem vindo ". $_SESSION["username"] ." \n\n Subscreveu o Evento ". $nomeEvento .". \n\nCumprimentos. \nSGC ";
           
    sendEmail($mysql, $emailPessoa, $subject, $message);
    
    header("location: ./evento.php?idEvento=".$idEvento);
} else {
    echo "Erro: (" . $stmtAdd->errno . ") " . $stmtAdd->error;
}
$stmtAdd->close();
$mysql->close();
?>