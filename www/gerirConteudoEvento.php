<?php
include(".htconfig.php");
$idEvento = (int) $_GET["idEvento"];
$idConteudo = $_GET["idConteudo"];
$idPessoa = $_SESSION["idPessoa"];

/* prepared statement - SQL Injection safe */
$stmtNomeEvento = $mysql->prepare("SELECT nomeEvento from Evento WHERE idEvento=?");
$stmtNomeEvento->bind_param('i', $idEvento);

// execute prepared statement
if ($stmtNomeEvento->execute()) {
    // bind result variables
    $stmtNomeEvento->bind_result($nomeEvento);
    while ($stmtNomeEvento->fetch()) {

    }
} else {
    echo "Erro: (" . $stmtNomeEvento->errno . ") " . $stmtNomeEvento->error;
}
$stmtNomeEvento->close();


if (isset($_POST['salvar'])){
    $stmt = $mysql->prepare("UPDATE Conteudo SET descricao = ? WHERE idConteudo = ?");
    $stmt->bind_param("si", $descricao, $idConteudo);
    $descricao = mysqli_real_escape_string($mysql, $_POST['descricao']);
    // execute prepared statement
    if ($stmt->execute()) {
        header("location: ./evento.php?idEvento=".$idEvento);
    } else {
        echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_POST['delete'])){
    $stmtNameConteudo = $mysql->prepare("SELECT nomeConteudo from Conteudo WHERE idConteudo=?");
    $stmtNameConteudo->bind_param("i", $idConteudo);
    // execute prepared statement
    if ($stmtNameConteudo->execute()) {
        // bind result variables
        $stmtNameConteudo->bind_result($nomeConteudo);
        while ($stmtNameConteudo->fetch()) {
            $filename = "./files/".$idPessoa."/".$nomeConteudo;
            unlink($filename);
        }
    } else {
        echo "Erro: (" . $stmtNameConteudo->errno . ") " . $stmtNameConteudo->error;
    }
    $stmtNameConteudo->close();
    
    $filename = $_POST['delete_file'];
    if (file_exists($filename)) {
      unlink($filename);
      echo 'File '.$filename.' has been deleted';
    } else {
      echo 'Could not delete '.$filename.', file does not exist';
    }
    
    $stmtDelete1 = $mysql->prepare("DELETE FROM `EventoConteudo` WHERE `idConteudo`=?");
    $stmtDelete1->bind_param("i", $idConteudo);
    // execute prepared statement
    if ($stmtDelete1->execute()) {
    } else {
        echo "Erro: (" . $stmtDelete1->errno . ") " . $stmtDelete1->error;
    }
    $stmtDelete1->close();
    
    $stmtDelete2 = $mysql->prepare("DELETE FROM `Conteudo` WHERE `idConteudo`=?");
    $stmtDelete2->bind_param("i", $idConteudo);
    // execute prepared statement
    if ($stmtDelete2->execute()) {
        header("location: ./evento.php?idEvento=".$idEvento);
    } else {
        echo "Erro: (" . $stmtDelete2->errno . ") " . $stmtDelete2->error;
    }
    $stmtDelete2->close();
}

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
            <li role="presentation" class="active"><a href="<?php echo './evento.php?idEvento='.$idEvento; ?>">Evento</a></li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                $stmtDescricao = $mysql->prepare("SELECT DISTINCT Conteudo.nomeConteudo, Conteudo.descricao from Conteudo INNER JOIN EventoConteudo ON EventoConteudo.idEvento=? WHERE Conteudo.idConteudo=?;");
                $stmtDescricao->bind_param('ii', $idEvento, $idConteudo);
                
                // execute prepared statement
                if ($stmtDescricao->execute()) {
                    // bind result variables
                    $stmtDescricao->bind_result($nomeConteudo, $descricao);
                    while ($stmtDescricao->fetch()) {
                        echo '<h3>Editar conteúdo "<i>' . $nomeConteudo . '</i>" do Evento '. $nomeEvento .' </h3><br/>';
                        echo '<h4>Descrição</h4>';
                        echo '<form method="POST">';
                        echo '<textarea class="form-control" id="descricao" name="descricao">'.$descricao.'</textarea>';
                        echo '<input type="submit" name="salvar" class="btn btn-default" value="Salvar">';
                        echo '</form>';
                        echo '<form method="POST">';
                        echo "<button style='float:right' class=\"btn btn-danger\" name='delete'><i class='glyphicon glyphicon-exclamation-sign'></i> Apagar Conteudo</button>";
                        echo '</form>';
                    }
                } else {
                    echo "Erro: (" . $stmtDescricao->errno . ") " . $stmtDescricao->error;
                }
                $stmtDescricao->close();
                ?>
            </div>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>
