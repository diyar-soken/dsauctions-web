<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_POST["username"]) || strlen(trim($_POST["username"]))==0 ||
            !isset($_POST["password"]) || strlen($_POST["password"])==0){
                throw new Exception("ERRORE, Username e Password obbligatori");
        }

        $username=trim($_POST["username"]);
        $password=$_POST["password"];

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);
        $username=$conn->real_escape_string($username);

        $sql="SELECT id_utente, username, password, email, ruolo
              FROM Utente
              WHERE username='$username'";
        $risultato=$conn->query($sql);

        if($risultato->num_rows!=1){
            throw new Exception("Credenziali ERRATE");
        }

        $riga=$risultato->fetch_assoc();

        if(!password_verify($password,$riga["password"])){
            throw new Exception("Credenziali ERRATE");
        }

        $_SESSION["id_utente"]=$riga["id_utente"];
        $_SESSION["username"]=$riga["username"];
        $_SESSION["email"]=$riga["email"];
        $_SESSION["ruolo"]=$riga["ruolo"];

        $risultato->free();
        $conn->close();
    }catch(mysqli_sql_exception $e){
        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        header("Location: login.php?msg=".urlencode($e->getMessage()));
        exit;
    }

    header("Location: index.php?msg=".urlencode("Accesso effettuato"));
    exit;
?>
