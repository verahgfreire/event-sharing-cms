<?php
include(".htconfig.php");

$idPessoa = $_SESSION["idPessoa"];

if (!empty($_POST['username']) || (!empty($_POST['pass1']) && !empty($_POST['pass2']))) {

    if (!empty($_POST['username'])) {
        if ((!empty($_POST['pass1']) && !empty($_POST['pass2']))) {
            $stmt = $mysql->prepare("UPDATE Pessoa SET username=?, password=? WHERE idPessoa=?;");
            $stmt->bind_param("ssi", $username, $password, $idPessoa);
        } else {
            $stmt = $mysql->prepare("UPDATE Pessoa SET username=? WHERE idPessoa=?;");
            $stmt->bind_param("si", $username, $idPessoa);
        }
    }

    $username = mysqli_real_escape_string($mysql, $_POST['username']);

    $pass1 = mysqli_real_escape_string($mysql, $_POST['pass1']);
    $pass2 = mysqli_real_escape_string($mysql, $_POST['pass2']);

    if (strcmp($pass1, $pass2) === 0) {
        $password = $pass1;

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>Informação atualizada com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Não foi possível atualizar informação de utilizador.</div>";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>Password inválida!</div>";
    }
    $stmt->close();
} if (isset($_POST['apagar'])) {
    $stmt = $mysql->prepare("UPDATE Pessoa SET ativo=0 WHERE idPessoa=?;");
    $stmt->bind_param("i", $idPessoa);

    if ($stmt->execute()) {
        echo "<div class='alert alert-danger' role='alert'>A sua conta foi apagada!</div>";
        header('location: ./logout.php');
    } else {
        echo "<div class='alert alert-danger' role='alert'>Não foi possível apagar a sua conta! Tente mais tarde!</div>";
    }
}
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
echo $site_name . " ";
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
            <li role="presentation" class="active"><a href="#">Configurações do utilizador</a></li>
        </ul>
        <div class="panel panel-default">
            <h3>Configurações do utilizador</h3>
            <div class="panel-body"> 
<?php
$idPessoa = $_SESSION["idPessoa"];

$stmt = $mysql->prepare("SELECT username, email FROM Pessoa WHERE idPessoa=?;");
$stmt->bind_param("i", $idPessoa);

if ($stmt->execute()) {
    $stmt->bind_result($username, $email);
    echo '<form method="post">';
    while ($stmt->fetch()) {
        echo '<h4>Username:</h2><input type="text" class="form-control" name="username" value="' . $username . '">';
        echo '<h4>Email:</h2><input type="email" class="form-control" disabled value="' . $email . '">';
        echo '<h4>Nova password:</h2><input type="password" class="form-control" name="pass1">';
        echo '<h4>Confirmar password:</h2><input type="password" class="form-control" name="pass2">';
    }
    echo "<br><input class=\"btn btn-default\" type='submit' value='Alterar Conta'>";
    echo "<button type=\"submit\" style='float:right' name=\"apagar\" class=\"btn btn-danger\"><i class='glyphicon glyphicon-exclamation-sign'></i> Apagar Conta</button></form>";
}
$stmt->close();
$mysql->close();
?>                
            </div>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
</body>
</html>