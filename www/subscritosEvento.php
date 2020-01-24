<?php
include(".htconfig.php");
require_once("lib-mail-v2.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <link rel="stylesheet" href="styles/geral.css">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
              integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
              integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <title><?php echo $site_name; ?></title>
    </head>
    <body>
        <div class="page-header">
            <h1>
                <?php
                echo $site_name;
                if (!isset($_SESSION['perfil'])) {
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./login.php'\" value='Login'>";
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./signup.php'\" value='Registo'>";
                } else {
                    echo "<small>Bem-vindo/a " . $_SESSION['username'] . "</small>";
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./logout.php'\" value='Log out'>";
                }
                ?></h1>

        </div>
        <ul class="nav nav-tabs">
            <li role="presentation"><a href="./index.php">Ínicio</a></li>
            <li role="presentation" class="active">
                <?php
                $stmtEvento = $mysql->prepare("SELECT idEvento, idPessoa, nomeEvento from Evento WHERE idEvento=?");
                $stmtEvento->bind_param('i', $idEvento);

                $idEvento = $_GET["idEvento"];
                echo "<a href='./evento.php?idEvento=" . $idEvento . "'>Evento</a></li>"
                ?>
        </ul>
        <div class="panel panel-default">

            <div class="panel-body">
                <?php
                // execute prepared statement
                if ($stmtEvento->execute()) {
                    // bind result variables
                    $stmtEvento->bind_result($idEvento, $idPessoa, $nome);
                    while ($stmtEvento->fetch()) {
                        echo '<h2> Subscritos - ' . $nome . '</h2>';
                    }
                } else {
                    echo "Erro: (" . $stmtEvento->errno . ") " . $stmtEvento->error;
                }
                $stmtEvento->close();


                $stmtPessoasEvento = $mysql->prepare("SELECT Pessoa.idPessoa, Pessoa.username, Pessoa.email, PessoaEvento.idPessoa, PessoaEvento.idEvento FROM PessoaEvento INNER JOIN Pessoa ON PessoaEvento.idPessoa=Pessoa.idPessoa WHERE PessoaEvento.idEvento = ?");
                $stmtPessoasEvento->bind_param('i', $idEvento);

                // execute prepared statement
                if ($stmtPessoasEvento->execute()) {
                    // bind result variables
                    $stmtPessoasEvento->bind_result($idPessoa, $username, $emailPessoa, $idPessoaT, $idEvento);

                    echo '<table id="idTabelaPessoasEvento">';
                    echo '<tr><th>Nome dos Subscritos</th></tr>';

                    while ($stmtPessoasEvento->fetch()) {
                        echo '<tr><td>' . $username . '</td></tr>';
                    }
                    echo '</table>';
                } else {
                    echo "Erro: (" . $stmtPessoasEvento->errno . ") " . $stmtPessoasEvento->error;
                }

                $stmtPessoasEvento->close();
                $mysql->close();
                ?>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
                integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
    </body>
</html>
