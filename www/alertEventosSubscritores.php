<?php

//include(".htconfig.php");
//require_once("sendMail.php");

////////saber o idEvento dos eventos que faltam 2 dias, enviar mail sempre que for 10h00

/*******Saber as pessoas que estao inscritas no evento********/
/*
$stmtPessoasEvento = $mysql->prepare("SELECT Pessoa.idPessoa, Pessoa.username, Pessoa.email, PessoaEvento.idPessoa, PessoaEvento.idEvento FROM PessoaEvento INNER JOIN Pessoa ON PessoaEvento.idPessoa=Pessoa.idPessoa WHERE PessoaEvento.idEvento = ?");
$stmtPessoasEvento->bind_param('i', $idEvento);

// execute prepared statement
if ($stmtPessoasEvento->execute()) {
    // bind result variables
    $stmtPessoasEvento->bind_result($idPessoa, $username, $emailPessoa, $idPessoaT, $idEvento);
} else {
    echo "Erro: (" . $stmtPessoasEvento->errno . ") " . $stmtPessoasEvento->error;
}

$stmtPessoasEvento->close();*/

/*******Saber o nome do evento********/
/*
$stmtNomeEvento = $mysql->prepare("SELECT nomeEvento FROM Evento WHERE idEvento=?");
$stmtNomeEvento->bind_param("i", $idEvento);

if ($stmtNomeEvento->execute()) {
    // bind result variables
    $stmtNomeEvento->bind_result($nomeEvento);

    $stmtNomeEvento->fetch();

} else {
    echo "Erro: (" . $stmtNomeEvento->errno . ") " . $stmtNomeEvento->error;
}
$stmtNomeEvento->close(); */

/*******Eviar o email********/
/*
$subject = "Evento " . $nomeEvento;
$message = "Caro/a " . $username . " \n\n Faltam apenas 2 dias para o Evento " . $nomeEvento . ", tenha um bom evento. \n\nCumprimentos. \nSGC ";

sendEmail($mysql, $emailPessoa, $subject, $message);

$mysql->close(); 
*/
?>
