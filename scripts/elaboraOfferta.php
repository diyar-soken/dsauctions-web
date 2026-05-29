<?php
    session_start();
    require_once "..\utils\config.php";

    $conn=null;

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accessp"));
            exit;
        }

        if($_SESSION["ruolo"]!="offerente"){
            throw new Exception("Solo gli offerenti possono fare offerte");
        }

        if(!isset($_POST["id_asta"]) ||
            filter_var($_POST["id_asta"], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>1)))===false ||
            !isset($_POST["importo"]) ||
            filter_var($_POST["importo"], FILTER_VALIDATE_FLOAT)===false){
                throw new Exception("ERRORE, Dati offerta non validi");
        }

        $id_asta=(int)$_POST["id_asta"];
        $id_offerente=(int)$_SESSION["id_utente"];
        $importo=(float)$_POST["importo"];

        if($importo<=0){
            throw new Exception("ERRORE, Importo non valido");
        }

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);
        $conn->begin_transaction();

        $sql="SELECT prezzo_corrente
              FROM Asta
              WHERE id_asta='$id_asta'
                    AND stato='aperta'
                    AND data_ora_inizio<=NOW()
                    AND data_ora_fine>=NOW()";
        $risultato=$conn->query($sql);

        if($risultato->num_rows==0){
            throw new Exception("ERRORE, Asta non trovata o non aperta");
        }

        $riga=$risultato->fetch_assoc();
        $prezzo_corrente=(float)$riga["prezzo_corrente"];
        $risultato->free();

        if($importo<=$prezzo_corrente){
            throw new Exception("ERRORE, L'Importo deve essere maggiore del prezzo corrente");
        }

        $importo_sql=$conn->real_escape_string($importo);

        $sql="INSERT INTO Offerta(importo, data_ora, id_offerente, id_asta)
              VALUES('$importo_sql', NOW(), '$id_offerente', '$id_asta')";
        $conn->query($sql);

        $sql="UPDATE Asta
              SET prezzo_corrente='$importo_sql'
              WHERE id_asta='$id_asta'";
        $conn->query($sql);

        $conn->commit();
        $conn->close();
    }catch(mysqli_sql_exception $e){
        if($conn!=null){
            $conn->rollback();
            $conn->close();
        }

        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        if($conn!=null){
            $conn->rollback();
            $conn->close();
        }

        header("Location: nuovaOfferta.php?msg=".urlencode($e->getMessage()));
        exit;
    }

    header("Location: dettaglioAsta.php?id_asta=".$id_asta."&msg=".urlencode("Offerta inserita correttamente"));
    exit;
?>
