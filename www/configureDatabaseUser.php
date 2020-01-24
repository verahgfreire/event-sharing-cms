<?php

include(".htconfigSystem.php");
include("createTables.php");

if (!empty($_POST['userBdName'])) {
    
    $mysql = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Check connection
    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }
    $mysql->set_charset("utf8");
    
    $userBdName = mysqli_real_escape_string($mysql,$_POST['userBdName']);
    $userBdPassword1 = mysqli_real_escape_string($mysql,$_POST['userBdPassword1']);
    $userBdPassword2 = mysqli_real_escape_string($mysql,$_POST['userBdPassword2']);
    $server = 'localhost';
    $userBdPassword = "";

    if (strcmp($userBdPassword1, $userBdPassword2) == 0) {
        $userBdPassword = $userBdPassword1;
        
        $sql = "CREATE USER '".$userBdName."'@'".$server."' IDENTIFIED BY '" .$userBdPassword."'";
        $mysql->query($sql);
        
        $sql = "GRANT USAGE ON * . * TO '".$userBdName."'@'".$server."' IDENTIFIED BY '".$userBdPassword."' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;";
        $mysql->query($sql);
        
        $sql = "GRANT ALL PRIVILEGES ON `TrabalhoSGC` . * TO '".$userBdName."'@'".$server."';";
        $mysql->query($sql);
        
        $server = '%';
        
        $sql = "CREATE USER '".$userBdName."'@'".$server."' IDENTIFIED BY '" .$userBdPassword."'";
        $mysql->query($sql);
        
        $sql = "GRANT USAGE ON * . * TO '".$userBdName."'@'".$server."' IDENTIFIED BY '".$userBdPassword."' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;";
        $mysql->query($sql);
        
        $sql = "GRANT ALL PRIVILEGES ON `TrabalhoSGC` . * TO '".$userBdName."'@'".$server."';";
        $mysql->query($sql);
    }
    
    $configFile = fopen(".htconfigSystem.php", "w") or die("Unable to open file!");
        $txt = "<?php ";
        $txt .= "\n \$db_host = '" . $db_host . "';";
        $txt .= "\n \$db_user = '" . $userBdName . "';";
        $txt .= "\n \$db_pass = '" . $userBdPassword . "';";
        $txt .= "\n \$db_name = 'TrabalhoSGC';";
        $txt .= "\n ?>";
        fwrite($configFile, $txt);
        fclose($configFile);
    
    createDatabaseTables($mysql);
    
    $mysql->close();
    
    header("location: ./configureEmailAccount.php");
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
            <h3>Configurações do utilizador da base de dados:</h3>
            <p>É também necessário criar o utilizador de acesso à base de dados.</p>
            <div class="form-group">
                <label for="userBdName">Nome de utilizador</label>
                <input type="text" name="userBdName" class="form-control" id="userBdName" placeholder="admin">
            </div>
            <div class="form-group">
                <label for="userBdPassword1">Password</label>
                <input type="password" name="userBdPassword1" class="form-control" id="userBdPassword1" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="userBdPassword2">Confirmar password</label>
                <input type="password" name="userBdPassword2" class="form-control" id="userBdPassword2" placeholder="Password">
            </div>
        </div>
        <button type="submit" class="btn btn-default" name="submit">Criar</button>

    </form>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>