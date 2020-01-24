<?php
include(".htconfig.php");
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
        <title><?php echo $site_name; ?></title>
    </head>
    <body>
        <div class="page-header">
            <h1>
                <?php
                echo $site_name . " ";
                if (!isset($_SESSION['perfil'])) {
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./signup.php'\" value='Registo'>";
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./login.php'\" value='Login'>";
                } else {
                    echo "<small>Bem-vindo/a " . $_SESSION['username'] . "</small>";
                    echo "<button style='float:right;' class=\"btn btn-default\" onclick=\"document.location.href='./editarUtilizador.php'\"><i class='glyphicon glyphicon-cog'></i></button>";
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./addCategoriasPessoa.php'\" value='Escolha Categorias'>";
                    echo "<input style='float:right;' class=\"btn btn-default\" type='button' onclick=\"document.location.href='./logout.php'\" value='Log out'>";
                }
                ?></h1>
        </div>
        <div>
            <ul class="nav nav-tabs">
                <li role="presentation"><a href="#">Ínicio</a></li>
                <?php
                if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0)) {
                    echo'<li role="presentation"><a href="./editarCategorias.php">Gerir Categorias</a></li>';
                }
                if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0)) {
                    echo'<li role="presentation"><a href="./gerirUtilizadores.php">Gerir Utilizadores</a></li>';
                }
                ?>
            </ul>
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="navbar-form navbar-left" method="post">
                        <div class="form-group">
                            <input type="text" id="textoProcura" name="textoProcura" size="155%" class="form-control" placeholder="Procurar">
                        </div>
                        <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>
                    </form>
                </div>
                <div class="panel panel-default" style="margin:0% 2.5% 1% 2.5%;">
                    <div class="panel-body"> 
                        <?php
                        if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0)) {
                            echo '<div style="float:right;">';
                            echo "<input type='button' class=\"btn btn-default\" onclick=\"document.location.href='./criarEvento.php'\" value=\"Criar Evento\">";

                            echo "</div>";
                        } else {
                            
                        }
                        ?>
                        <h3>Eventos</h3>
                        <div class="row">
                            <?php
                            if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0 || strcmp($_SESSION['perfil'], 'utilizador') == 0)) {
                                if (isset($_POST['textoProcura']) && strcmp($_POST['textoProcura'], "") != 0) {
                                    $stmt = $mysql->prepare("SELECT Evento.idEvento, Evento.idPessoa, Evento.nomeEvento, Evento.publico, Evento.descricao, Evento.dataEvento, Evento.horaEvento FROM 
                                    ((Evento INNER JOIN EventoCategoria ON Evento.idEvento=EventoCategoria.idEvento)
                                    INNER JOIN Categoria ON Categoria.idCategoria=EventoCategoria.idCategoria)
                                    WHERE (Evento.nomeEvento LIKE ? OR Evento.descricao LIKE ? OR Categoria.nomeCategoria LIKE ?) GROUP BY Evento.idEvento ORDER BY Evento.dataEvento DESC;");
                                    $stmt->bind_param('sss', $procura, $procura, $procura);
                                    $procura = mysqli_real_escape_string($mysql, $_POST['textoProcura']);
                                    $procura = '%'.$procura.'%';
                                } else {
                                    $stmt = $mysql->prepare("SELECT * FROM Evento ORDER BY Evento.dataEvento DESC;");
                                }
                            } else {
                                if (isset($_POST['textoProcura']) && strcmp($_POST['textoProcura'], "") != 0) {
                                    $stmt = $mysql->prepare("SELECT Evento.idEvento, Evento.idPessoa, Evento.nomeEvento, Evento.publico, Evento.descricao, Evento.dataEvento, Evento.horaEvento FROM 
                                    ((Evento INNER JOIN EventoCategoria ON Evento.idEvento=EventoCategoria.idEvento)
                                    INNER JOIN Categoria ON Categoria.idCategoria=EventoCategoria.idCategoria)
                                    WHERE (Evento.nomeEvento LIKE ? OR Evento.descricao LIKE ? OR Categoria.nomeCategoria LIKE ?) AND Evento.publico=1 GROUP BY Evento.idEvento ORDER BY Evento.dataEvento DESC;");
                                    $stmt->bind_param('sss', $procura, $procura, $procura);
                                    $procura = mysqli_real_escape_string($mysql, $_POST['textoProcura']);
                                    $procura = '%'.$procura.'%';
                                } else {
                                    $stmt = $mysql->prepare("SELECT * FROM Evento WHERE publico=1 ORDER BY Evento.dataEvento DESC;");
                                }
                            }

                            // execute prepared statement
                            if ($stmt->execute()) {
                                // bind result variables
                                $stmt->bind_result($idEvento, $idPessoa, $nomeEvento, $publico, $descricao, $dataEvento, $horaEvento);
                                
                                // fetch value
                                while ($stmt->fetch()) {
                                    echo '<div id="eventos" class="col-sm-6 col-md-4">';
                                    echo '<div class="thumbnail">';
                                    echo '<div class="caption">';
                                    if (strlen($nomeEvento) > 27) {
                                        $nomeEvento = substr($nomeEvento, 0, 25) . "...";
                                    }
                                    echo '<h3>' . $nomeEvento . '</h3>';
                                    if (strlen($descricao) > 50) {
                                        $descricao = substr($descricao, 0, 48) . "...";
                                    }
                                    echo '<p>' . $descricao . '</p>';
                                    echo '<p><a href="./evento.php?idEvento=' . $idEvento . '" class="btn btn-primary" role="button">Saber mais</a></p>';
                                    echo '</div></div></div>';
                                }
                            } else {
                                echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
                            }

                            $stmt->close();
                            $mysql->close();
                            ?>

                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <?php if (isset($_POST['textoProcura'])){echo '<script>document.getElementById("textoProcura").value ="'. $_POST['textoProcura'].'"</script>';} ?>
    </body>
</html>
