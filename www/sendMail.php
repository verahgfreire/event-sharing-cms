<?php
require_once("lib-mail-v2.php");

function sendEmail($mysql, $emailPessoa, $subject, $message) {
    
    /////////////////////////////////////// SEND EMAIL ///////////////////////////////////////
    $stmtEmailAccount = $mysql->prepare("SELECT * from emailAccounts");

    // execute prepared statement
    if ($stmtEmailAccount->execute()) {
        // bind result variables
        $stmtEmailAccount->bind_result($id, $accountName, $useSSL, $smtpServer, $port, $timeout, $loginName, $email, $password, $displayName);

        while ($stmtEmailAccount->fetch()) {

            $sendEmail = sendAuthEmail($smtpServer, $useSSL, $port, $timeout, $loginName, $password, $email, $loginName, $emailPessoa, NULL, NULL, $subject, $message, $showProtocol = false, NULL);

            if ($sendEmail == true) {
                echo "Email enviado";
            } else {
                echo "Email nao enviado";
            }
        }
    } else {
        echo "Erro: (" . $stmtEmailAccount->errno . ") " . $stmtEmailAccount->error;
    }
    $stmtEmailAccount->close();
    
}
?>
