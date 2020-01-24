<?php
include(".htconfig.php");

$idPessoa = $_SESSION["idPessoa"];

$stmtCategoriasPessoa = $mysql->prepare("SELECT idCategoria FROM PessoaCategoria WHERE idPessoa=?;");
$stmtCategoriasPessoa->bind_param("i", $idPessoa);

if ($stmtCategoriasPessoa->execute()) {
    $stmtCategoriasPessoa->bind_result($idCategoria);

    $CategoriasPessoa = array();
    while ($stmtCategoriasPessoa->fetch()) {
        $CategoriasPessoa[$idCategoria] = $idCategoria;
    }
} else {
    echo "Erro: (" . $stmtCategoriasPessoa->errno . ") " . $stmtCategoriasPessoa->error;
}
$stmtCategoriasPessoa->close();

if (!empty($_POST['categorias'])) {
    $stmtDeleteCategoriasPessoa = $mysql->prepare("DELETE FROM PessoaCategoria WHERE idPessoa = ?;");
    $stmtDeleteCategoriasPessoa->bind_param("i", $idPessoa);
    $stmtDeleteCategoriasPessoa->execute();
    $stmtDeleteCategoriasPessoa->fetch();
    $stmtDeleteCategoriasPessoa->close();

    foreach ($_POST['categorias']as $idCategoria) {
        $stmtAddPessoaCategoria = $mysql->prepare("INSERT INTO pessoacategoria (idPessoa, idCategoria) VALUES (?,?)");
        $stmtAddPessoaCategoria->bind_param("ii", $idPessoa, $idCategoria);
        if ($stmtAddPessoaCategoria->execute()) {
            header("location: ./index.php");
        } else {
            echo "Erro: (" . $stmtAddPessoaCategoria->errno . ") " . $stmtAddPessoaCategoria->error;
        }
        $stmtAddPessoaCategoria->close();
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
            <h1>Escolha de Categorias</h1>
        </div>
        <div>
            <ul class="nav nav-tabs">
                <li role="presentation"><a href="./index.php">Ínicio</a></li>
            </ul>
        </div>
        <form action="./addCategoriasPessoa.php " method="POST">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Selecione as categorias que deseja seguir: </h3>
                    <?php
                    /* prepared statement - SQL Injection safe */
                    $stmtCategorias = $mysql->prepare("SELECT * from Categoria");
                    if ($stmtCategorias->execute()) {
                        // bind result variables
                        $stmtCategorias->bind_result($id, $nome, $idPrincipal);

                        echo '<dl>';
                        // fetch value
                        while ($stmtCategorias->fetch()) {

                            $checked = "";
                            if (array_key_exists($id, $CategoriasPessoa)) {
                                $checked = "checked";
                            }
                            if ($idPrincipal === 0) {
                                echo '<div id="catSecundarias' . $id . '">';
                                echo '<dt><input name="categorias[]" id="' . $id . '" type="checkbox"  value="' . $id . '" ' . $checked . '> ' . $nome . '</dt>';
                                echo '</div>';
                            } else {
                                echo '<script type="text/javascript">';
                                echo 'document.getElementById("catSecundarias' . $idPrincipal . '").innerHTML += "<dd><input  name=\"categorias[]\" type=\"checkbox\" value=\"' . $id . '\" ' . $checked . '> ' . $nome . '</dd>";';
                                echo '</script>';
                            }
                        }
                        echo '</dl>';
                    } else {
                        echo "Erro: (" . $stmtCategorias->errno . ") " . $stmtCategorias->error;
                    }
                    $stmtCategorias->close();
                    ?>
                    <input type="submit" class="btn btn-default" value="Salvar">
                </div>
            </div>
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
