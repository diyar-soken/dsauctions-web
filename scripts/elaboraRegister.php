<?php
    session_start();
    require_once "..\utils\config.php";

    $conn=null;

    try{
        if(!isset($_POST["username"]) || strlen(trim($_POST["username"]))==0 ||
            !isset($_POST["email"]) || filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)===false ||
            !isset($_POST["password"]) || strlen($_POST["password"])<4 ||
            !isset($_POST["nome"]) || strlen(trim($_POST["nome"]))==0 ||
            !isset($_POST["cognome"]) || strlen(trim($_POST["cognome"]))==0){
                throw new Exception("ERRORE, Dati di registrazione non validi");
        }

        $username=trim($_POST["username"]);
        $email=trim($_POST["email"]);
        $password=$_POST["password"];
        $nome=trim($_POST["nome"]);
        $cognome=trim($_POST["cognome"]);
        $password_hash=password_hash($password,PASSWORD_DEFAULT);

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);
        $conn->begin_transaction();

        $username=$conn->real_escape_string($username);
        $email=$conn->real_escape_string($email);
        $password_hash=$conn->real_escape_string($password_hash);
        $nome=$conn->real_escape_string($nome);
        $cognome=$conn->real_escape_string($cognome);

        $sql="INSERT INTO Utente(username, password, email, ruolo)
              VALUES('$username','$password_hash','$email','offerente')";
        $conn->query($sql);

        $id_utente=$conn->insert_id;

        $sql="INSERT INTO Offerente(id_utente, nome, cognome)
              VALUES('$id_utente','$nome','$cognome')";
        $conn->query($sql);

        $conn->commit();
        $conn->close();
    }catch(mysqli_sql_exception $e){
        if($conn!=null){
            $conn->rollback();
            $conn->close();
        }

        if($e->getCode()==1062){
            header("Location: register.php?msg=".urlencode("Username oppure E-Mail già utilizzati"));
            exit;
        }

        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        if($conn!=null){
            $conn->rollback();
            $conn->close();
        }

        header("Location: register.php?msg=".urlencode($e->getMessage()));
        exit;
    }

    header("Location: login.php?msg=".urlencode("Registrazione effettuata, ora puoi accedere"));
    exit;
?>
