<?php
include(".htconfig.php");
require_once("alertEventoNovo.php");

if (!empty($_POST['nome'])) {

    /* prepared statement - SQL Injection safe */
    $stmt = $mysql->prepare("INSERT INTO Evento (idPessoa, nomeEvento, publico, descricao, dataEvento, horaEvento) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("isisss", $idPessoa, $nome, $publico, $descricao, $data, $hora);

    $nome = mysqli_real_escape_string($mysql, $_POST['nome']);
    $descricao = mysqli_real_escape_string($mysql, $_POST['descricao']);
    $p = mysqli_real_escape_string($mysql, $_POST['publico']);
    if (isset($p)) {
        $publico = 1;
    } else {
        $publico = 0;
    }
    $data = mysqli_real_escape_string($mysql, $_POST['data']);
    $hora = mysqli_real_escape_string($mysql, $_POST['hora']);
    $idPessoa = $_SESSION["idPessoa"];

// execute prepared statement
    if ($stmt->execute()) {
        $idEvento = $stmt->insert_id;
        $alertEventoNovoPorCategoria = False;
        foreach ($_POST['categorias'] as $idCategoria) {
            $alertEventoNovoPorCategoria = True;
            $stmt2 = $mysql->prepare("INSERT INTO EventoCategoria (idEvento, idCategoria) VALUES (?,?)");
            $stmt2->bind_param("ii", $idEvento, $idCategoria);
            if ($stmt2->execute()) {
                
            } else {
                echo "Erro: (" . $stmt2->errno . ") " . $stmt2->error;
            }
            $stmt2->close();
        }
        if($alertEventoNovoPorCategoria === TRUE){
            alertEventoNovo($mysql, $idEvento, $nome, $data);
        }
        header("location: ./evento.php?idEvento=" . $idEvento . "&nomeEvento=" . $nome);
    } else {
        echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
    }
    $stmt->close();
    $mysql->close();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <link rel="stylesheet" href="styles/geral.css">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <title><?php echo $site_name;?></title>
    </head>
    <body>
        <div class="page-header">
            <h1>Criar Evento</h1>
        </div>
        <form action="./criarEvento.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome do evento</label>
                <input type="text" name="nome" class="form-control" id="nome" >
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao"></textarea>
            </div>
            <div class="form-group">
                <label for="data">Data</label>
                <input type="date" name="data" class="form-control" id="data" placeholder="dd/mm/aaaa">
            </div>
            <div class="form-group">
                <label for="hora">Hora</label>
                <input type="time" name="hora" class="form-control" id="hora" placeholder="hh:mm">
            </div>
            <label><input type="checkbox" value="publico" name="publico"> Evento Público</label>
            <br>

            <h4>Categorias do evento</h4>
            <div>
                <?php
                /* prepared statement - SQL Injection safe */
                $stmt3 = $mysql->prepare("SELECT * from Categoria");

                // execute prepared statement
                if ($stmt3->execute()) {
                    // bind result variables
                    $stmt3->bind_result($id, $nome, $idPrincipal);

                    echo '<table>';
                    // fetch value
                    while ($stmt3->fetch()) {

                        if ($idPrincipal === 0) {
                            echo '<tr>';
                            echo '<td><label><input name="categorias[]" id="' . $id . '" onclick="getSecondaryCategory(' . $id . ')" type="checkbox" value="' . $id . '"> ' . $nome . '</label></td>';
                            echo '<td><div id="catSecundarias' . $id . '" style="color:#888;display:none"></div></td>';
                            echo '</tr>';
                        } else {
                            echo '<script type="text/javascript">';
                            echo 'document.getElementById("catSecundarias' . $idPrincipal . '").innerHTML += "<label><input name=\"categorias[]\" type=\"checkbox\" value=\"' . $id . '\"> ' . $nome . '</label>";';
                            echo '</script>';
                        }
                    }
                    echo '</table>';
                } else {
                    echo "Erro: (" . $stmt3->errno . ") " . $stmt3->error;
                }

                $stmt3->close();
                $mysql->close();
                ?>
            </div>
            <button type="submit" class="btn btn-default">Submeter</button>
        </form>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script type="text/javascript">
            function getSecondaryCategory(id) {
                var display = document.getElementById("catSecundarias" + id).style.display;
                if (display === 'block') {
                    document.getElementById("catSecundarias" + id).style.display = 'none';
                    var checks = document.getElementsByTagName('input');
                    for (var i = 0; i < checks.length; i++) {
                        checks[i].checked = false;
                    }
                } else {
                    document.getElementById("catSecundarias" + id).style.display = 'block';
                }
            }
        </script>
    </body>
</html>
