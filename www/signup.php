<?php
include(".htconfig.php");
require_once("sendMail.php");

if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {

    /* prepared statement - SQL Injection safe */
    $stmt = $mysql->prepare("INSERT INTO Pessoa (username, email, password, nomePerfil) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $username, $emailPessoa, $passwordPessoa, $perfil);

    $username = mysqli_real_escape_string($mysql, $_POST['username']);
    $emailPessoa = mysqli_real_escape_string($mysql, $_POST['email']);
    $pass1 = mysqli_real_escape_string($mysql, $_POST['pass1']);
    $pass2 = mysqli_real_escape_string($mysql, $_POST['pass2']);
    $perfil = 'utilizador';

    $captcha = $_POST['captcha'];

    if ($_SESSION['captcha'] == $_POST['captcha']) {

        if (strcmp($pass1, $pass2) == 0) {
            $passwordPessoa = $pass1;

            // execute prepared statement
            if ($stmt->execute()) {
                $idPessoa = $stmt->insert_id;
                // Set session variables
                $_SESSION["idPessoa"] = $idPessoa;
                $_SESSION["username"] = $username;

                mkdir('./files/' . $idPessoa);

                $subject = "Confirmação de Inscrição";
                $message = "Bem vindo " . $username . ",\n\n Este e-mail foi usado para criar uma nova conta no nosso site. Para confirmar o registo confirme em http://localhost/trabalho/ativarConta.php?idPessoa=".$idPessoa." \n\n Cumprimentos. \n SGC ";

                sendEmail($mysql, $emailPessoa, $subject, $message);

                header("location: ./index.php");
            } else {
                echo "<div class='alert alert-danger' role='alert'>Não foi possivel criar o novo utilizador.</div>";
            }

            $stmt->close();
            $mysql->close();
        } else {
            echo '<script type="text/javascript"> alert("Password inválida."); </script>';
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>Captcha incorrecto!</div>";
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
            <h1>Registo</h1>
        </div>
        <form action="./signup.php" method="POST">
            <div class="form-group">
                <label for="exampleInputUsername1">Username</label>
                <input type="text" name="username" class="form-control" id="exampleInputUsername1" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="pass1" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword2">Confirmar Password</label>
                <input type="password" name="pass2" class="form-control" id="exampleInputPassword2" placeholder="Password">
            </div>
            <div style="margin-bottom: 1%">
                <?php
                $debug = false;

                if ($debug == false) {
                    $captchaValue = @substr(md5(time()), 0, 9);
                } else {
                    $captchaValue = @substr(md5("abcdfeghi"), 0, 9);
                }

                if ($debug == true) {
                    $value = "value=\"" . $captchaValue . "\"";
                    echo "<p>Debug is active</p>";
                } else {
                    $value = "value=\"\"";
                }
                ?>
                <img src="captchaImage.php"/><br>
                <label for="captcha">Digite o código</label><br>
                <input type="text" name="captcha" <?php echo $value; ?> id="captcha"><br>
            </div>
            <button type="submit" class="btn btn-default" name="submit">Submeter</button>

        </form>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
