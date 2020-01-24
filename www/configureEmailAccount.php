<?php
include(".htconfigSystem.php");

if (!empty($_POST['accountName']) && !empty($_POST['displayName']) && !empty($_POST['emailSite']) 
        && !empty($_POST['emailServer']) && !empty($_POST['emailPort']) 
        && !empty($_POST['emailPass1']) && !empty($_POST['emailPass2'])) {
    
    $mysql = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Check connection
    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }
    $mysql->set_charset("utf8");
    
    /* prepared statement - SQL Injection safe */
    $stmt2 = $mysql->prepare("INSERT INTO emailAccounts (accountName, useSSL, smtpServer, port, timeout, loginName, email, password, displayName) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt2->bind_param("sisiissss", $accountName, $useSSL, $emailServer, $emailPort, $timeout, $loginName, $emailSite, $password, $displayName);

    $accountName = mysqli_real_escape_string($mysql, $_POST['accountName']);
    $displayName = mysqli_real_escape_string($mysql, $_POST['displayName']);
    $emailSite = mysqli_real_escape_string($mysql, $_POST['emailSite']);
    $loginName = $emailSite;
    $emailServer = mysqli_real_escape_string($mysql, $_POST['emailServer']);
    $emailPort = mysqli_real_escape_string($mysql, $_POST['emailPort']);
    $useSSL = 1;
    $timeout = 30;
    $pass1 = mysqli_real_escape_string($mysql, $_POST['emailPass1']);
    $pass2 = mysqli_real_escape_string($mysql, $_POST['emailPass2']);

    if (strcmp($pass1, $pass2) == 0) {
        $password = $pass1;

        // execute prepared statement
        if ($stmt2->execute()) {
           
            if (!file_exists('./files')) {
                mkdir('./files');
            }
            
            header("location: ./configureAdministrator.php");
        } else {
            echo "Erro: (" . $stmt2->errno . ") " . $stmt2->error;
        }

        $stmt2->close();
    } else {
        echo '<script type="text/javascript"> alert("Password inválida."); </script>';
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
        <title>Configuração do site</title>
    </head>
    <body>
        <h1>Configuração do sistema de gestão de conteúdos</h1>
        <form method="POST">
            <h3>Email para correio eletrónico do site:</h3>
            <div class="form-group">
                <label for="accountName">Nome da conta</label>
                <input type="text" name="accountName" class="form-control" id="accountName" placeholder="GMAIL">
            </div>
            <div class="form-group">
                <label for="displayName">Nome a apresentar</label>
                <input type="text" name="displayName" class="form-control" id="displayName" placeholder="smi20162017">
            </div>
            <div class="form-group">
                <label for="emailSite">Email</label>
                <input type="email" name="emailSite" class="form-control" id="emailSite" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="emailServer">Servidor de email</label>
                <input type="text" name="emailServer" class="form-control" id="emailServer" placeholder="smtp.gmail.com">
            </div>
            <div class="form-group">
                <label for="emailPort">Porto de email</label>
                <input type="text" name="emailPort" class="form-control" id="emailPort" placeholder="465">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="emailPass1" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword2">Confirmar Password</label>
                <input type="password" name="emailPass2" class="form-control" id="exampleInputPassword2" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-default" name="submit">Criar o meu site</button>
        </form>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
