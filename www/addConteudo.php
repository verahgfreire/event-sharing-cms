<?php

include(".htconfig.php");

if (isset($_FILES["fileToUpload"]["name"])) {
        upload($mysql);
}

function upload($mysql) {
    $idPessoa = $_SESSION["idPessoa"];
    $pastaDestino = "files/" . $idPessoa . "/";
    $nomeAleatorio = $_FILES["fileToUpload"]["name"]; //sha1_file($_FILES["fileToUpload"]["name"]);
    $ficheiroDestino = $pastaDestino . basename($nomeAleatorio);
    $arrayNomeFicheiro = explode(".", $nomeAleatorio);
    $length = count($arrayNomeFicheiro);
    $tipoFicheiro = $arrayNomeFicheiro[$length-1];

    $uploadOk = 1;
    $imageFileType = pathinfo($ficheiroDestino, PATHINFO_EXTENSION);

// Check if file size isnt bigger than 1GB
    if ($_FILES["fileToUpload"]["size"] > 1000000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    $tipo = '';
    // Allow certain file formats
    if (strcmp($tipoFicheiro, 'jpg')===0 | strcmp($tipoFicheiro, 'jpeg')===0 | strcmp($tipoFicheiro, 'png')===0 | strcmp($tipoFicheiro, 'gif')===0) {
        $tipo = 'img';
    } elseif (strcmp($tipoFicheiro, 'mp4')===0 | strcmp($tipoFicheiro, 'webm')===0) {
        $tipo = 'video';
    } elseif (strcmp($tipoFicheiro, 'mp3')===0 | strcmp($tipoFicheiro, 'wav')===0) {
        $tipo = 'audio';
    } else {
        echo "Sorry, only JPG, JPEG, PNG, GIF, MP4, WEBM, MP3 & WAV files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk === 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $ficheiroDestino)) {

            echo '<div class=\"alert alert-success\" role=\"alert\"><p>The file ' . basename($_FILES["fileToUpload"]["name"]) . ' has been uploaded.</p></div>';
            getVisibilidadeEvento($mysql, $idPessoa, $nomeAleatorio, $tipo);
        } else {
            echo '<div class=\"alert alert-danger\" role=\"alert\">Sorry, there was an error uploading your file.</div>';
        }
    }
}

function getVisibilidadeEvento($mysql, $idPessoa, $nomeAleatorio, $tipo) {
    /* prepared statement - SQL Injection safe */
    $stmt = $mysql->prepare("SELECT publico FROM Evento WHERE idEvento=?");
    $stmt->bind_param("i", $idEvento);

    $idEvento = mysqli_real_escape_string($mysql, $_GET['idEvento']);
    // execute prepared statement
    if ($stmt->execute()) {
        // bind result variables
        $stmt->bind_result($p);
        // fetch value
        $stmt->fetch();
        $stmt->close();

        addConteudo($mysql, $idPessoa, $p, $idEvento, $nomeAleatorio, $tipo);
    } else {
        echo "Erro: (" . $stmt->errno . ") " . $stmt->error;
        $stmt->close();
    }
}

function addConteudo($mysql, $idPessoa, $p, $idEvento, $nomeAleatorio, $tipo) {
    /* prepared statement - SQL Injection safe */
    $stmt2 = $mysql->prepare("INSERT INTO Conteudo (idPessoa, nomeConteudo, descricao, publico, tipo) VALUES (?,?,?,?,?)");
    $stmt2->bind_param("issis", $idPessoa, $nomeAleatorio, $descricao, $publico, $tipo);

    if ($p === '1') {
        $publico = 1;
    } else {
        $publico = 0;
    }

    $descricao = mysqli_real_escape_string($mysql, $_POST['descricao']);

    // execute prepared statement
    if ($stmt2->execute()) {
        $idConteudo = $stmt2->insert_id;

        addLigacaoConteudoEvento($mysql, $idEvento, $idConteudo);
    } else {
        echo "Erro: (" . $stmt2->errno . ") " . $stmt2->error;
    }


    $stmt2->close();
}

function addLigacaoConteudoEvento($mysql, $idEvento, $idConteudo) {
    /* prepared statement - SQL Injection safe */
    $stmt3 = $mysql->prepare("INSERT INTO EventoConteudo (idEvento, idConteudo) VALUES (?,?);");
    $stmt3->bind_param("ii", $idEvento, $idConteudo);

    // execute prepared statement
    if ($stmt3->execute()) {
        header("location: ./evento.php?idEvento=".$idEvento);
    } else {
        echo "Erro: (" . $stmt3->errno . ") " . $stmt3->error;
    }

    $stmt3->close();
}

$mysql->close();
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
        <title><?php echo $site_name;?></title>
    </head>
    <body>
        <div class="page-header">
            <h1>Adicionar Conteudo</h1>
        </div>
        <form action="<?php echo './addConteudo.php?idEvento=' . $_GET['idEvento'] ?>" method="post"enctype="multipart/form-data">

            <div class="form-group">
                <label for="selectImage">Selecionar imagem:</label>
                <input type="file" name="fileToUpload" id="fileToUpload">
            </div><br/>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao"></textarea>
            </div><br/>
            <input type="submit" value="Upload Image" name="submit" class="btn btn-default">
        </form>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
                integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
    </body>
</html>
