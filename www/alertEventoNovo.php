<?php

require_once("sendMail.php");

function alertEventoNovo($mysql, $idEvento, $nomeEvento, $dataEvento) {

    echo $idEvento .", ". $nomeEvento .", ".  $dataEvento;
    
    /******* Saber o nome e o email das pessoas, têm as mesmas categorias que o evento criado ********/
    $stmtPessoasCategoriasEvento = $mysql->prepare("SELECT DISTINCT Pessoa.username, Pessoa.email FROM Pessoa INNER JOIN PessoaCategoria ON PessoaCategoria.idPessoa = Pessoa.idPessoa INNER JOIN EventoCategoria ON PessoaCategoria.idCategoria=EventoCategoria.idCategoria WHERE EventoCategoria.idEvento = ?;");
    $stmtPessoasCategoriasEvento->bind_param('i', $idEvento);

    if ($stmtPessoasCategoriasEvento->execute()) {
        $stmtPessoasCategoriasEvento->bind_result($username, $emailPessoa);
        // fetch value
        $emailPessoas = array();
        $usernames = array();
        $i= 0;
        while ($stmtPessoasCategoriasEvento->fetch()){
            
            $emailPessoas[$i] = $emailPessoa;
            $usernames[$i] = $username;
            $i = $i+1;
        }
    } else {
        echo "Erro: (" . $stmtPessoasCategoriasEvento->errno . ") " . $stmtPessoasCategoriasEvento->error;
    }
    $stmtPessoasCategoriasEvento->close();
    
    /******* Eviar o email ********/
    $arrlength = count($usernames);
    for($x = 0; $x < $arrlength; $x++) {
        echo 'email da pessoa: ' . $emailPessoas[$x]."\n";
        $subject = "Novo Evento " . $nomeEvento;
        $message = "Caro/a " . $usernames[$x] . " \n\n De acordo com os seus gostos, recomendamos que vá a este Evento, " . $nomeEvento . ", que irá ser realizado no dia " . $dataEvento . ". \n\nCumprimentos. \nSGC ";
        sendEmail($mysql, $emailPessoas[$x], $subject, $message);
    }
    
}

?>
