<?php
include(".htconfig.php");
//ADICIONAR CATEGORIA
if (!empty($_POST['nomeCategoria'])) {
    /* prepared statement - SQL Injection safe */
    $stmt = $mysql->prepare("INSERT INTO categoria (nomeCategoria, idPrincipal) VALUES (?,?)");
    $stmt->bind_param("si", $nomeCategoria, $idPrincipal);

    $nomeCategoria = mysqli_real_escape_string($mysql, $_POST['nomeCategoria']);

    $sec = mysqli_real_escape_string($mysql, $_POST['idPrincipal']);
    if ($sec === 'true') {
        $idPrincipal = $idCategoria;
    } else {
        $idPrincipal = 0;
    }
    $idPessoa = $_SESSION["idPessoa"];

// execute prepared statement
    if ($stmt->execute()) {
        $idCategoria = $stmt->insert_id;

        $stmt2 = $mysql->prepare("INSERT INTO pessoacategoria (idPessoa, idCategoria) VALUES (?,?)");
        $stmt2->bind_param("ii", $idPessoa, $idCategoria);
        if ($stmt2->execute()) {
            
        } else {
            echo "Erro: (" . $stmt2->errno . ") " . $stmt2->error;
        }
        $stmt2->close();
        header("location: ./editarCategorias.php");
    } else {
        echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
    }

    $stmt->close();
} else if (!empty($_POST['nomeSubCategoria'])) {
    $stmt5 = $mysql->prepare("INSERT INTO categoria (nomeCategoria, idPrincipal) VALUES (?,?)");
    $stmt5->bind_param("si", $nomeSubCategoria, $idPrincipal);

    $nomeSubCategoria = mysqli_real_escape_string($mysql, $_POST['nomeSubCategoria']);

    $idPrincipal = mysqli_real_escape_string($mysql, $_POST['nomeCatPrincipal']);


    $idPessoa = $_SESSION["idPessoa"];

// execute prepared statement
    if ($stmt5->execute()) {
        $idCategoria = $stmt5->insert_id;

        $stmt6 = $mysql->prepare("INSERT INTO pessoacategoria (idPessoa, idCategoria) VALUES (?,?)");
        $stmt6->bind_param("ii", $idPessoa, $idCategoria);
        if ($stmt6->execute()) {
            
        } else {
            echo "Erro: (" . $stmt6->errno . ") " . $stmt6->error;
        }
        $stmt6->close();
        header("location: ./editarCategorias.php");
    } else {
        echo "Erro: (" . $stmt5->errno . ") " . $stmt5->error;
    }
    $stmt5->close();
} else if (!empty($_POST['categorias'])) {
    foreach ($_POST['categorias']as $idCategoria) {
        $stmt7 = $mysql->prepare("SELECT COUNT(*) FROM eventocategoria WHERE idCategoria = ?");
        $stmt7->bind_param("i", $idCategoria);
        $stmt7->execute();
        $stmt7->bind_result($nEventoCategoria);
        $stmt7->fetch();
        $stmt7->close();
        if ((int) $nEventoCategoria === 0) {
            $stmt8 = $mysql->prepare("DELETE FROM pessoacategoria WHERE idCategoria = ?");
            $stmt8->bind_param("i", $idCategoria);
            if ($stmt8->execute()) {
                $stmt9 = $mysql->prepare("DELETE FROM categoria WHERE idCategoria = ?");
                $stmt9->bind_param("i", $idCategoria);
                $stmt9->execute();
            } else {
                echo "Erro: (" . $stmt8->errno . ") " . $stmt8->error;
            };
            $stmt8->close();
            $stmt9->close();
        } else {
            echo "<script>alert('Não foi possivel apagar. A categoria está a ser utilizada')</script>";
        }
    }
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
            <h1>Gestão de Categorias</h1>
        </div>
        <div>
            <ul class="nav nav-tabs">
                <li role="presentation"><a href="./index.php">Ínicio</a></li>
                <li role="presentation" class="active"><a href="#">Gerir Categorias</a></li>
            </ul>
        </div>

        <div class="panel panel-default">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <form action="./editarCategorias.php" method="POST">

                                <div class="panel-body" > 
                                    <h3><b>Adicionar Categorias</b></h3>
                                    <div class="form-group">
                                        <!-- Apenas o administrador pode adicionar novas categorias-->
                                        <?php
                                        if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 )) {
                                            echo '<div>';
                                            echo' <label for="nomeCategoria"> Adicionar Categoria</label>';
                                            echo' <input name="nomeCategoria" type="text" class="form-control" id="nomeCategoria">';
                                            echo '<button type="submit" class="btn btn-default">Adicionar</button>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>

                            </form>
                            <form action="./editarCategorias.php" method="POST">
                                <div class="panel-body"> 
                                    <div class="form-group">
                                        <label for="nomeSubCategoria">Adicionar SubCategoria</label>
                                        <input name="nomeSubCategoria" type="text" class="form-control" id="nomeSubCategoria">
                                        <label>Categoria a que pertence</label>
                                        <div class="form-group">
                                            <!-- Adicionar novas subcategorias-->
                                            <?php
                                            /* prepared statement - SQL Injection safe */
                                            $stmt3 = $mysql->prepare("SELECT * from Categoria");
// execute prepared statement
                                            if ($stmt3->execute()) {
                                                // bind result variables
                                                $stmt3->bind_result($id, $nome, $idPrincipal);
                                                echo '<select name="nomeCatPrincipal" id="nomeCatPrincipal">';
                                                // fetch value
                                                while ($stmt3->fetch()) {
                                                    if ($idPrincipal === 0) {
                                                        echo '<option value="' . $id . '">' . $nome . '</option>';
                                                    }
                                                }
                                                echo'</select>';
                                            } else {
                                                echo "Erro: (" . $stmt3->errno . ") " . $stmt3->error;
                                            }
                                            $stmt3->close();
                                            ?>
                                            <button type="submit" class="btn btn-default">Adicionar</button>
                                        </div> 
                                    </div>
                                </div>
                            </form>
                        </div>

                        <form action="./editarCategorias.php" method="POST">
                            <?php
                            if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 )) {
                                echo '<div class="panel panel-default">';
                                echo '<div class="panel-body">';
                                echo '<h3><b>Remover Categorias</b></h3>';

                                /* prepared statement - SQL Injection safe */
                                $stmt4 = $mysql->prepare("SELECT * from Categoria");
                                // execute prepared statement
                                if ($stmt4->execute()) {
                                    // bind result variables
                                    $stmt4->bind_result($id, $nome, $idPrincipal);

                                    echo '<dl>';
                                    // fetch value
                                    while ($stmt4->fetch()) {

                                        if ($idPrincipal === 0) {
                                            echo '<div id="catSecundarias' . $id . '">';
                                            echo '<dt><input name="categorias[]" id="' . $id . '" type="checkbox"  value="' . $id . '"> ' . $nome . '</dt>';
                                            echo '</div>';
                                        } else {

                                            echo '<script type="text/javascript">';
                                            echo 'document.getElementById("catSecundarias' . $idPrincipal . '").innerHTML += "<dd><input  name=\"categorias[]\" type=\"checkbox\" value=\"' . $id . '\"> ' . $nome . '</dd>";';
                                            echo '</script>';
                                        }
                                    }
                                    echo '</dl>';
                                } else {
                                    echo "Erro: (" . $stmt4->errno . ") " . $stmt4->error;
                                }
                                $stmt4->close();

                                echo'<input type="submit" class="btn btn-default" value="Remover">';
                            }
                            echo '</div>';
                            echo '</div>';
                            ?>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3><b>Todas as Categorias</b></h3>
                                    <?php
                                    /* prepared statement - SQL Injection safe */
                                    $stmt1 = $mysql->prepare("SELECT * from categoria");

                                    // execute prepared statement
                                    if ($stmt1->execute()) {
                                        // bind result variables
                                        $stmt1->bind_result($id, $nomeCategoria, $idPrincipal);
                                        // fetch value
                                        echo'<div class="panel panel-default">';
                                        while ($stmt1->fetch()) {

                                            if ($idPrincipal === 0) {
                                                echo '<div id="catSecundarias1' . $id . '" class="panel-heading">';
                                                echo '<h3 class="panel-title">' . $nomeCategoria . '</h3>';
                                                echo '</div>';
                                            } else {
                                                echo '<div id="catSecundarias1' . $id . '">';
                                                echo '<script type="text/javascript">';
                                                echo 'document.getElementById("catSecundarias1' . $idPrincipal . '").innerHTML += "<li>' . $nomeCategoria . '</li>";';
                                                echo '</script>';
                                                echo '</div>';
                                            }
                                        }
                                        echo'</div>';
                                    } else {
                                        echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
                                    }
                                    $stmt1->close();
                                    $mysql->close();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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