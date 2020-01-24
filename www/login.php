<?php
include(".htconfig.php");

if (!empty($_POST['email']) &&!empty($_POST['password'])) {

    /* prepared statement - SQL Injection safe */
    $stmt = $mysql->prepare("SELECT idPessoa, username, nomePerfil FROM Pessoa WHERE email=? AND password=? AND ativo=1");
    $stmt->bind_param("ss", $email, $password);

    $email = mysqli_real_escape_string($mysql, $_POST['email']);
    $password = mysqli_real_escape_string($mysql, $_POST['password']);

    if ($stmt->execute()) {
        // bind result variables
        $stmt->bind_result($idPessoa, $username, $perfil);

        // fetch value
        while ($stmt->fetch()){
            // Set session variables
            $_SESSION["idPessoa"] = $idPessoa;
            $_SESSION["username"] = $username;
            $_SESSION["perfil"] = $perfil;

            header("location: ./index.php");
        } 
    } else {
        echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
    }

    $stmt->close();
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
        <title><?php echo $site_name;?></title>
    </head>
    <body>
        <div class="page-header">
        <h1>Log in</h1>
        </div>
        <div>
            <form class="form-inline" action="./login.php" method="POST">
                <div class="form-group">
                    <label for="exampleInputName2">Email</label>
                    <input name="email" type="email" class="form-control" id="exampleInputName2" placeholder="email">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">Password</label>
                    <input name="password" type="password" class="form-control" id="exampleInputEmail2" placeholder="password">
                </div>
                <button type="submit" class="btn btn-default">Log in</button>
            </form>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>