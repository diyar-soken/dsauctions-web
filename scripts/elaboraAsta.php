<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="amministratore"){
            throw new Exception("Solo gli amministratori possono creare aste");
        }

        if(!isset($_POST["id_auto"]) ||
            filter_var($_POST["id_auto"], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>1)))===false ||
            !isset($_POST["prezzo_base"]) ||
            filter_var($_POST["prezzo_base"], FILTER_VALIDATE_FLOAT)===false ||
            !isset($_POST["data_ora_inizio"]) || strlen($_POST["data_ora_inizio"])==0 ||
            !isset($_POST["data_ora_fine"]) || strlen($_POST["data_ora_fine"])==0){
                throw new Exception("ERRORE, Dati asta non validi");
        }

        $id_auto=(int)$_POST["id_auto"];
        $prezzo_base=(float)$_POST["prezzo_base"];
        $data_ora_inizio=str_replace("T"," ",$_POST["data_ora_inizio"]).":00";
        $data_ora_fine=str_replace("T"," ",$_POST["data_ora_fine"]).":00";

        if($prezzo_base<=0){
            throw new Exception("ERRORE, Prezzo base non valido");
        }

        if(strtotime($data_ora_fine)<=strtotime($data_ora_inizio)){
            throw new Exception("ERRORE, Data fine non valida");
        }

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $prezzo_base_sql=$conn->real_escape_string($prezzo_base);
        $data_ora_inizio=$conn->real_escape_string($data_ora_inizio);
        $data_ora_fine=$conn->real_escape_string($data_ora_fine);

        $sql="INSERT INTO Asta(prezzo_base, prezzo_corrente, data_ora_inizio, data_ora_fine, stato, id_auto)
              VALUES('$prezzo_base_sql','$prezzo_base_sql','$data_ora_inizio','$data_ora_fine','aperta','$id_auto')";
        $conn->query($sql);

        $conn->close();
    }catch(mysqli_sql_exception $e){
        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        header("Location: nuovaAsta.php?msg=".urlencode($e->getMessage()));
        exit;
    }

    header("Location: index.php?msg=".urlencode("Asta creata correttamente"));
    exit;
?>
