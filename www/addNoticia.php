<?php

include(".htconfig.php");

/* prepared statement - SQL Injection safe */
$stmt = $mysql->prepare("INSERT INTO Noticia (noticia, idEvento) VALUES (?,?)");
$stmt->bind_param("si", $noticia, $idEvento);

$noticia = mysqli_real_escape_string($mysql, $_POST['noticia']);
$idEvento = mysqli_real_escape_string($mysql, $_GET['idEvento']);

if ($stmt->execute()) {
    header('location: ./evento.php?idEvento='.$idEvento);
}
$stmt->close();
$mysql->close();
?>