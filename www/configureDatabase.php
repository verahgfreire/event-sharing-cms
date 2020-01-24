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
        <title>Configuração do site</title>
    </head>
    <body>
        <h1>Configuração do sistema de gestão de conteúdos</h1>
        <form method="POST">
            <div>
                <h3>Configurações da base de dados:</h3>
                <p>Primeiro é necessário criar a base de dados do nosso sistema. 
                    Insira abaixo as credenciais de acesso ao motor de base de dados para a criação da mesma.</p>
                <div class="form-group">
                    <label for="bdServer">Nome do servidor</label>
                    <input type="text" name="bdServer" class="form-control" id="bdServer" placeholder="localhost">
                </div>
                <div class="form-group">
                    <label for="bdName">Nome de utilizador</label>
                    <input type="text" name="bdName" class="form-control" id="bdName" placeholder="root">
                </div>
                <div class="form-group">
                    <label for="bdPassword">Password</label>
                    <input type="text" name="bdPassword" class="form-control" id="bdPassword" placeholder="Password">
                </div>
            </div>
            <button type="submit" class="btn btn-default" name="submit">Criar</button>
        </form>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>

<?php
if (!empty($_POST['bdServer']) && !empty($_POST['bdName'])) {

    $db_host = $_POST['bdServer'];
    $db_user = $_POST['bdName'];
    $db_pass = $_POST['bdPassword'];
    
    mysqli_report(MYSQLI_REPORT_STRICT);

    try {
        $mysql = new mysqli($db_host, $db_user, $db_pass);

        /* prepared statement - SQL Injection safe */
        $stmt = $mysql->prepare("CREATE DATABASE IF NOT EXISTS `TrabalhoSGC` CHARACTER SET utf8 COLLATE utf8_unicode_ci;");

        // execute prepared statement
        if ($stmt->execute()) {

            $configFile = fopen(".htconfigSystem.php", "w") or die("Unable to open file!");
            $txt = "<?php ";
            $txt .= "\n \$db_host = '" . $db_host . "';";
            $txt .= "\n \$db_user = '" . $db_user . "';";
            $txt .= "\n \$db_pass = '" . $db_pass . "';";
            $txt .= "\n \$db_name = 'TrabalhoSGC';";
            $txt .= "\n ?>";
            fwrite($configFile, $txt);
            fclose($configFile);

            header("location: ./configureDatabaseUser.php");
        } else {
            echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
        }

        $stmt->close();
        $mysql->close();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger' role='alert'>As credenciais inseridas não estão correctas.</div>";
    }
}
?>