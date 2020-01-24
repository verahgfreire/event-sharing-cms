<?php

include(".htconfig.php");
$idEvento = (int) $_GET["idEvento"];

$stmt = $mysql->prepare("SELECT conteudo.idPessoa, conteudo.nomeConteudo FROM conteudo INNER JOIN EventoConteudo ON Conteudo.idConteudo=EventoConteudo.idConteudo WHERE EventoConteudo.idEvento=?");
$stmt->bind_param('i', $idEvento);

$files = array();

if ($stmt->execute()) {
    $stmt->bind_result($idPessoa, $nomeConteudo);

    while ($stmt->fetch()) {
        array_push($files, 'files/' . $idPessoa . '/' . $nomeConteudo);
    }
    
        # create new zip opbject
        $zip = new ZipArchive();

        # create a temp file & open it
        $tmp_file = tempnam('.', '');
        $zip->open($tmp_file, ZipArchive::CREATE);

        # loop through each file
        foreach ($files as $file) {

            # download file
            $download_file = file_get_contents($file);

            #add it to the zip
            $zip->addFromString(basename($file), $download_file);
        }

        # close zip
        $zip->close();

        # send the file to the browser as a download
        header('Content-disposition: attachment; filename=ConteudosEvento' . $idEvento . '.zip');
        header('Content-type: application/zip');
        readfile($tmp_file);
    
}
$stmt->close();
$mysql->close();
?>