<?php

include(".htconfig.php");

$file = basename($_GET['nomeConteudo']);
$filePath = 'files/' . $_GET['idPessoa'] . '/' . $file;


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-lenght: ' . filesize($filePath));
header('Content-Disposition: attachment; filename="' . $file . '"');
//readfile("$filePath");
$f = fopen($filePath, "rb");
fpassthru($f);
fclose($f);
?>