<?php
include(".htconfig.php");
require_once("alertEventoNovo.php");

if (isset($_POST['submit'])) {

    /* prepared statement - SQL Injection safe */
    $sql = "SELECT idPessoa from Pessoa WHERE NOT idPessoa=".$_SESSION['idPessoa'];
    $result = $mysql->query($sql);

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            
            $idPessoa =  $row['idPessoa'];
            
            echo $idPessoa;

            $nomePerfil = $_POST['' . $idPessoa];

            echo $nomePerfil;

            /* prepared statement - SQL Injection safe */
            $stmt = $mysql->prepare("UPDATE Pessoa SET nomePerfil = ? WHERE idPessoa = ?");
            $stmt->bind_param("si", $nomePerfil, $idPessoa);



            // execute prepared statement
            if ($stmt->execute()) {

                header("location: ./index.php");
            } else {
                echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        
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
        <title><?php echo $site_name; ?></title>
    </head>
    <body>
        <div class="page-header">
            <h1>Gerir Utilizadores</h1>
        </div>
        <div class="panel panel-default">
            <div class="panel-body"> 
                <div class="row">
                    <?php
                    if (isset($_SESSION['perfil']) && (strcmp($_SESSION['perfil'], 'administrador') == 0 )) {
                        $stmt = $mysql->prepare("SELECT idPessoa, username, nomePerfil FROM Pessoa WHERE NOT idPessoa=?");
                        $stmt->bind_param("i", $id);
                        
                        $id = $_SESSION['idPessoa'];
                        // execute prepared statement
                        if ($stmt->execute()) {
                            // bind result variables
                            $stmt->bind_result($idPessoa, $username, $nomePerfil);

                            echo '<form method="post">';
                            echo '<div id="utilizadores">';
                            // fetch value
                            while ($stmt->fetch()) {
                                echo '<div value="' . $idPessoa . '">';
                                echo '<h4>' . $username . '</h4>';
                                if (strcmp($nomePerfil, "administrador") == 0) {
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="administrador" checked>Administrador</label>';
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="simpatizante">Simpatizante</label>';
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="utilizador">Utilizador</label>';
                                } else if (strcmp($nomePerfil, "simpatizante") == 0) {
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="administrador">Administrador</label>';
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="simpatizante" checked>Simpatizante</label>';
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="utilizador">Utilizador</label>';
                                } else if (strcmp($nomePerfil, "utilizador") == 0) {
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="administrador">Administrador</label>';
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="simpatizante">Simpatizante</label>';
                                    echo '<label><input type="radio" name="' . $idPessoa . '" value="utilizador" checked>Utilizador</label>';
                                }
                                echo '</div>';
                                echo '<br>';
                            }
                            echo '</div>';
                            echo '<input type="submit" name="submit" class="btn btn-default" value="Atualizar permissões">';
                            echo '</form>';
                        } else {
                            echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
                        }

                        $stmt->close();
                        $mysql->close();
                    }
                    ?>

                </div>
            </div> 
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>