<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="amministratore"){
            throw new Exception("Solo gli amministratoriz possono creare aste");
        }

        $id_amministratore=(int)$_SESSION["id_utente"];

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);
        $sql="SELECT id_auto, marca, descrizione, anno
              FROM Auto
              WHERE id_amministratore='$id_amministratore'
              ORDER BY marca";
        $risultato=$conn->query($sql);

        if($risultato->num_rows==0){
            header("Location: nuovaAuto.php?msg=".urlencode("prima devi inserire un auto"));
            exit;
        }
    }catch(mysqli_sql_exception $e){
        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }catch(Exception $e){
        header("Location: errore.php?msg=".urlencode($e->getMessage()));
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/stile.css">
    <title>nuova asta</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1 class="h4 mb-0">Creazione nuova asta</h1>
            </div>
            <div class="card-body">
                <?php
                    if(isset($_GET['msg'])){
                        echo "<div class='alert alert-warning'>";
                        echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                        echo "</div>";
                    }
                ?>

                <form action="elaboraAsta.php" method="POST">
                    <div class="mb-3">
                        <label for="id_auto" class="form-label">Auto</label>
                        <select id="id_auto" name="id_auto" class="form-select">
                            <?php
                                while($riga=$risultato->fetch_assoc()){
                                    $id_auto=htmlspecialchars($riga["id_auto"],ENT_QUOTES,"UTF-8");
                                    $marca=htmlspecialchars($riga["marca"],ENT_QUOTES,"UTF-8");
                                    $descrizione=htmlspecialchars($riga["descrizione"],ENT_QUOTES,"UTF-8");
                                    $anno=htmlspecialchars($riga["anno"],ENT_QUOTES,"UTF-8");

                                    echo "<option value='$id_auto'>".$marca." ".$anno." - ".$descrizione."</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="prezzo_base" class="form-label">Prezzo base</label>
                        <input id="prezzo_base" name="prezzo_base" type="number" step="0.01" min="1" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="data_ora_inizio" class="form-label">Data e ora di inizio</label>
                        <input id="data_ora_inizio" name="data_ora_inizio" type="datetime-local" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="data_ora_fine" class="form-label">Data e ora di fine</label>
                        <input id="data_ora_fine" name="data_ora_fine" type="datetime-local" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Crea asta</button>
                    <button type="reset" class="btn btn-secondary">Azzera</button>
                    <a href="index.php" class="btn btn-link">Torna alla home</a>
                </form>
            </div>
        </div>
    </div>

    <?php
        $risultato->free();
        $conn->close();
    ?>
</body>
</html>
