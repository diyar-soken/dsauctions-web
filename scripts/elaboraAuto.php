<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="amministratore"){
            throw new Exception("Solo gli amministratori possono inserire auto");
        }

        if(!isset($_POST["marca"]) || strlen(trim($_POST["marca"]))==0 ||
            !isset($_POST["descrizione"]) || strlen(trim($_POST["descrizione"]))==0 ||
            !isset($_POST["anno"]) ||
            filter_var($_POST["anno"], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>1950, "max_range"=>2030)))===false){
                throw new Exception("ERRORE, Dati auto non validi");
        }

        $marca=trim($_POST["marca"]);
        $descrizione=trim($_POST["descrizione"]);
        $anno=(int)$_POST["anno"];
        $id_amministratore=(int)$_SESSION["id_utente"];

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $marca=$conn->real_escape_string($marca);
        $descrizione=$conn->real_escape_string($descrizione);

        $sql="INSERT INTO Auto(marca, descrizione, anno, id_amministratore)
              VALUES('$marca','$descrizione','$anno','$id_amministratore')";
        $conn->query($sql);

        $conn->close();
    }catch(mysqli_sql_exception $e){
        if($e->getCode()==1062){
            header("Location: nuovaAuto.php?msg=".urlencode("Marca gia presente: è consentita una sola auto per marca"));
            exit;
        }

        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        header("Location: nuovaAuto.php?msg=".urlencode($e->getMessage()));
        exit;
    }

    header("Location: index.php?msg=".urlencode("Auto inserita correttamente"));
    exit;
?>
