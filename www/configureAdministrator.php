<?php
include(".htconfigSystem.php");
require_once( "lib-mail-v2.php" );

if (!empty($_POST['nomeSite']) && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {
    
    $mysql = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Check connection
    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }
    $mysql->set_charset("utf8");

    /* prepared statement - SQL Injection safe */
    $stmt = $mysql->prepare("INSERT INTO Pessoa (username, email, password, nomePerfil, ativo) VALUES (?,?,?,?,1)");
    $stmt->bind_param("ssss", $username, $emailPessoa, $passwordPessoa, $perfil);

    $username = mysqli_real_escape_string($mysql, $_POST['username']);
    $emailPessoa = mysqli_real_escape_string($mysql, $_POST['email']);
    $pass1 = mysqli_real_escape_string($mysql, $_POST['pass1']);
    $pass2 = mysqli_real_escape_string($mysql, $_POST['pass2']);
    $perfil = 'administrador';
    
    $site_name = mysqli_real_escape_string($mysql,$_POST['nomeSite']);

    if (strcmp($pass1, $pass2) == 0) {
        $passwordPessoa = $pass1;

        // execute prepared statement
        if ($stmt->execute()) {
            $idPessoa = $stmt->insert_id;

            mkdir('./files/' . $idPessoa);


            //////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////////////// SEND EMAIL ///////////////////////////////////////
            $stmtEmailAccount = $mysql->prepare("SELECT * from emailAccounts");

            // execute prepared statement
            if ($stmtEmailAccount->execute()) {
                // bind result variables
                $stmtEmailAccount->bind_result($id, $accountName, $useSSL, $smtpServer, $port, $timeout, $loginName, $email, $password, $displayName);

                while ($stmtEmailAccount->fetch()) {
                    //envio de email para o utilizador novo na base de dados
                    $subject = "Sistema de gestão de conteúdos";
                    $message = "Bem vindo! \n\n Esta conta de email foi associada como administrador do seu site.";
                    echo $email . $password . $subject;
                    $sendEmail = sendAuthEmail($smtpServer, $useSSL, $port, $timeout, $loginName, $password, $email, $loginName, $emailPessoa, NULL, NULL, $subject, $message, $showProtocol = false, NULL);

                    if ($sendEmail == true) {
                        echo "Email enviado";
                    } else {
                        echo "Email não enviado";
                    }

                    $configFile = fopen(".htconfigSite.php", "w") or die("Unable to open file!");
                    $txt = "<?php ";
                    $txt .= "\n \$site_name = '" . $site_name . "';";
                    $txt .= "\n ?>";
                    fwrite($configFile, $txt);
                    fclose($configFile);

                    header("location: ./index.php");
                }
            } else {
                echo "Erro: (" . $stmt3->errno . ") " . $stmt3->error;
            }
            $stmtEmailAccount->close();
            //////////////////////////////////////////////////////////////////////////////////////////
        } else {
            echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo '<script type="text/javascript"> alert("Password inválida."); </script>';
    }
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
        <title>Configuração do site</title>
    </head>
    <body>
        <h1>Configuração do sistema de gestão de conteúdos</h1>
        <form method="POST">
            <h3>Nome do site:</h3>
            <input type='text' placeholder='Nome' id='nomeSite' name='nomeSite' class="form-control">
            <h3>Criar administrador</h3>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="password1">Password</label>
                <input type="password" name="pass1" class="form-control" id="password1" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="password2">Confirmar Password</label>
                <input type="password" name="pass2" class="form-control" id="password2" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-default" name="submit">Criar o meu site</button>

        </form>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
