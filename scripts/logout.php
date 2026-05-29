<?php
    session_start();
    session_unset();
    session_destroy();

    header("Location: index.php?msg=".urlencode("Uscita effettuata"));
    exit;
?>
