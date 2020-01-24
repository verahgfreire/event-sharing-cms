<?php
include(".htconfig.php");
$idEvento = (int) $_GET["idEvento"];

$stmtNomeCategorias = $mysql->prepare("SELECT Categoria.nomeCategoria FROM Categoria INNER JOIN EventoCategoria ON Categoria.idCategoria=EventoCategoria.idCategoria WHERE EventoCategoria.idEvento = ?;");
$stmtNomeCategorias->bind_param('i', $idEvento);
$categorias = '';
if ($stmtNomeCategorias->execute()) {
    $stmtNomeCategorias->bind_result($nomeCategoria);
    while ($stmtNomeCategorias->fetch()) {
        $categorias .= ' #' . $nomeCategoria;
    }
} else {
    echo "Erro: (" . $stmtNomeCategorias->errno . ") " . $stmtNomeCategorias->error;
}
$stmtNomeCategorias->close();
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
            <li role="presentation" class="active"><a href="#">Evento</a></li>
        </ul>
        <div class="panel panel-default">
            <?php
            if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0 || strcmp($_SESSION['perfil'], 'utilizador') == 0)) {
                $idPessoa = $_SESSION["idPessoa"];
                echo '<div style = "float:right; padding: 1%; margin: 1%; height: 100%;">';
                
                //echo '<form class="form-inline" method="get" action="./evento.php?idEvento=' . $idEvento . '">';
                $stmt = $mysql->prepare("SELECT COUNT(*) FROM PessoaEvento WHERE idPessoa = ? AND idEvento = ?");
                $stmt->bind_param('ii', $idPessoa, $idEvento);

                if ($stmt->execute()) {
                    $stmt->bind_result($countPessoaEvento);
                    while ($stmt->fetch()) {
                        if ((int) $countPessoaEvento === 0) {
                            $valueSubscrever = 'Subscrever';
                            echo "<input type = \"button\" onclick=\"document.location.href='./addSubscreverEvento.php?idEvento=" . $_GET["idEvento"] . "'\" style = \"float:right;\" class = \"btn btn-default\" value = " . $valueSubscrever . ">";
                        } else {
                            $valueSubscrever = 'Subscrito';
                            echo "<input type = \"button\" onclick=\"document.location.href='./removeSubscreverEvento.php?idEvento=" . $_GET["idEvento"] . "'\" style = \"float:right;\" class = \"btn btn-default\" value = " . $valueSubscrever . ">";
                        }
                    }
                }
                $stmt->close();

                echo "<input type = \"button\" onclick=\"document.location.href='./subscritosEvento.php?idEvento=" . $_GET["idEvento"] . "'\" style = \"float:right;\" class = \"btn btn-default\" value = \"Subscritores\">";
                //echo '</form>';
                echo '</div>';
            }
            ?>

            <div class="panel-body">
                <?php
                    /* prepared statement - SQL Injection safe */
                    $stmt3 = $mysql->prepare("SELECT * from Evento WHERE idEvento=?");
                    $stmt3->bind_param('i', $idEvento);

                    // execute prepared statement
                    if ($stmt3->execute()) {
                        // bind result variables
                        $stmt3->bind_result($idEvento, $idPessoa, $nome, $publico, $descricao, $data, $hora);
                        while ($stmt3->fetch()) {
                            if ($publico == 1 || isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0 || strcmp($_SESSION['perfil'], 'utilizador') == 0)) {
                                echo '<h2>' . $nome . '</h2>';
                                echo '<p>' . $descricao . $categorias . '</p>';
                                if ($publico === 0) {
                                    echo '<b>Evento Privado</b>';
                                } else {
                                    echo '<b>Evento Público</b>';
                                }
                                echo '<p>' . $data . ' ' . $hora . '</p>';
                            } else {
                                echo 'Não tem permissão para visualizar este evento.';
                            }
                        }
                    } else {
                        echo "Erro: (" . $stmt3->errno . ") " . $stmt3->error;
                    }

                    $stmt3->close();
                
                ?>
            </div>
            <div class="panel-body"> 
                <?php
                if ($publico == 1 || isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0 || strcmp($_SESSION['perfil'], 'utilizador') == 0)) {
                    if (isset($_SESSION['perfil']) && strcmp($_SESSION['perfil'], 'utilizador') != 0) {
                        echo "<input type = \"button\" onclick=\"adicionarNoticia();\" style = \"float:right;\" class = \"btn btn-default\" id=\"addNoticia\" value = \"Adicionar notícia\">";
                    }
                    echo '<div id="myModal" class="modal"><div class="modal-content"><span class="close">&times;</span>'
                    . '<h3>Adicionar notícia ao evento</h3>'
                    . '<form action="./addNoticia.php?idEvento='.$_GET['idEvento'].'" method="POST">'
                    . '<textarea class="form-control" name="noticia" placeholder="Escreve aqui a nova noticia"></textarea>'
                    . '<input type="submit" name="submit" class="btn btn-default" value="Adicionar"></form></div></div>';
                    echo '<h3>Notícias</h3>';

                    $stmt = $mysql->prepare("SELECT idNoticia, noticia FROM Noticia WHERE idEvento=?");
                    $stmt->bind_param('i', $idEvento);
                    if ($stmt->execute()) {
                        $stmt->bind_result($idNoticia, $noticia);

                        while ($stmt->fetch()) {
                            echo '<div class="list-group"><a href="#" class="list-group-item">';
                            echo '<p name="' . $idNoticia . '" class="list-group-item-text">' . $noticia . '</p>';
                            echo '</a></div>';
                        }
                    }
                    $stmt->close();
                }
                ?>

                <?php
                if ($publico == 1 || isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 || strcmp($_SESSION['perfil'], 'simpatizante') == 0 || strcmp($_SESSION['perfil'], 'utilizador') == 0)) {
                    if (isset($_SESSION['perfil']) && strcmp($_SESSION['perfil'], 'utilizador') != 0) {
                        echo "<input type = \"button\" onclick=\"document.location.href='./addConteudo.php?idEvento=" . $_GET["idEvento"] . "'\" style = \"float:right;\" class = \"btn btn-default\" value = \"Adicionar conteúdo\">";
                    }
                    echo '<h3>Conteúdos</h3>';
                    $stmt10 = $mysql->prepare("SELECT Conteudo.idConteudo, conteudo.idPessoa, conteudo.nomeConteudo, conteudo.descricao, conteudo.tipo FROM conteudo INNER JOIN EventoConteudo ON Conteudo.idConteudo=EventoConteudo.idConteudo WHERE EventoConteudo.idEvento=?");
                    $stmt10->bind_param('i', $idEvento);
                    if ($stmt10->execute()) {
                        $stmt10->bind_result($idConteudo, $idPessoa, $nomeConteudo, $descricao, $tipo);

                        $bool = TRUE; 
                        while ($stmt10->fetch()) {
                            if($bool){
                                if(isset($_SESSION['perfil'])){
                                echo '<p><a href="./downloadTodosConteudos.php?idEvento=' . $idEvento . '" class="btn btn-primary" role="button">Descarregar todos os conteúdos</a></p>';
                                }
                                $bool = FALSE;
                            }
                            echo '<div class="row">';
                            echo '<div class="col-sm-6 col-md-3">';
                            echo '<div class="thumbnail">';
                            $fechar = 'controls></' . $tipo . '>';
                            if (strcmp($tipo, 'img') === 0) {
                                $fechar = '>';
                            }
                            echo'<' . $tipo . ' src="files/' . $idPessoa . '/' . $nomeConteudo . '" height="200" width="250"' . $fechar;
                            echo'<div class="caption">';
                            echo'<h4>Legenda:</h4>';
                            echo'<p>' . $descricao . '</p>';
                            if(isset($_SESSION['perfil'])){
                            echo '<p><a href="./downloadConteudoSimples.php?idPessoa=' . $idPessoa . '&nomeConteudo=' . $nomeConteudo . '" class="btn btn-primary" role="button">Descarregar</a></p>';
                            }
                            if(isset($_SESSION['perfil']) && strcmp($_SESSION['perfil'], 'administrador') == 0 || isset($_SESSION['perfil']) && strcmp($_SESSION['perfil'], 'simpatizante') == 0){
                               echo "<input type = \"button\" onclick=\"document.location.href='./gerirConteudoEvento.php?idEvento=" . $_GET["idEvento"] .'&idConteudo=' . $idConteudo . "'\" class='btn btn-primary' value = \"Editar Conteúdo\">"; 
                            }
                            echo'</div></div></div>';
                        }
                        echo'</div>';
                    }
                    $stmt10->close();
                    $mysql->close();
                }
                ?>
            </div>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        function adicionarNoticia() {
            // Get the modal
            var modal = document.getElementById('myModal');

            // Get the button that opens the modal
            var btn = document.getElementById("addNoticia");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks the button, open the modal 
            btn.onclick = function () {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>