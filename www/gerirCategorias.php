<?php
include(".htconfig.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <title><?php echo $site_name; ?></title>
    </head>
    <body>
        <div class="page-header">
            <h1>Gestão de Categorias</h1>
        </div>
        <form action="./gerirCategorias.php" method="POST">

            <?php
            echo "<input class=\"btn btn-default\" type='button' value='Ver Todas as Categorias'>";
            echo "<input class=\"btn btn-default\" type='button' onclick=\"document.location.href='./editarCategorias.php'\" value='Editar Categorias'>";
            ?>
       
            <div>
                <?php
                /* prepared statement - SQL Injection safe */
                $stmt = $mysql->prepare("SELECT * from categoria");

                // execute prepared statement
                if ($stmt->execute()) {
                    // bind result variables
                    $stmt->bind_result($id, $nomeCategoria, $idPrincipal);
                    // fetch value
                    while ($stmt->fetch()) {
                  
                        if ($idPrincipal === 0) {
                            echo'<dl>';
                            echo '<div id="catSecundarias' . $id . '">';
                            echo '<dt>' . $nomeCategoria . '</dt>';
                            echo '</div>';

                            
                        } else {
                            echo'<dl>';
                            echo '<script type="text/javascript">';
                            echo 'document.getElementById("catSecundarias' . $idPrincipal . '").innerHTML += "<dd>' . $nomeCategoria . '</dd>";';
                            echo '</script>';
                            echo'</dl>';
                           
                        }
                        echo '</dl>';
               
                    }
                } else {
                    echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
                }
                $stmt->close();
                $mysql->close();
                ?>
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
