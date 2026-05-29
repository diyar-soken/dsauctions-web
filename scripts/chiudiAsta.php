<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="amministratore"){
            throw new Exception("Solo gli Amministratori possono chiudere aste");
        }

        if(!isset($_POST["id_asta"]) ||
            filter_var($_POST["id_asta"], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>1)))===false){
                throw new Exception("ERRORE, Asta non valida");
        }

        $id_asta=(int)$_POST["id_asta"];

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $sql="UPDATE Asta SET stato='chiusa' WHERE id_asta='$id_asta'";
        $conn->query($sql);

        if($conn->affected_rows==0){
            throw new Exception("ERRORE, Asta non chiusa");
        }

        $conn->close();
    }catch(mysqli_sql_exception $e){
        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        header("Location: index.php?msg=".urlencode($e->getMessage()));
        exit;
    }

    header("Location: index.php?msg=".urlencode("Asta chiusa correttamente"));
    exit;
?>
