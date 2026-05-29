<?php
    session_start();
    require_once "..\utils\config.php";

    $asta_selezionata=0;

    if(isset($_GET["id_asta"]) &&
        filter_var($_GET["id_asta"], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>1)))!==false){
            $asta_selezionata=(int)$_GET["id_asta"];
    }

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="offerente"){
            throw new Exception("Solo gli offerenti possono fare offerte");
        }

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $sql="SELECT Asta.id_asta, Asta.prezzo_corrente, Auto.marca, Auto.descrizione, Auto.anno
              FROM Asta INNER JOIN Auto ON Asta.id_auto=Auto.id_auto
              WHERE Asta.stato='aperta'
                    AND Asta.data_ora_inizio<=NOW()
                    AND Asta.data_ora_fine>=NOW()
              ORDER BY Auto.marca";
        $risultato=$conn->query($sql);

        if($risultato->num_rows==0){
            header("Location: index.php?msg=".urlencode("non ci sono aste aperte"));
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
    <title>nuova offerta</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1 class="h4 mb-0">Nuova offerta</h1>
            </div>
            <div class="card-body">
                <?php
                    if(isset($_GET['msg'])){
                        echo "<div class='alert alert-warning'>";
                        echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                        echo "</div>";
                    }
                ?>

                <form action="elaboraOfferta.php" method="POST">
                    <div class="mb-3">
                        <label for="id_asta" class="form-label">Asta</label>
                        <select id="id_asta" name="id_asta" class="form-select">
                            <?php
                                while($riga=$risultato->fetch_assoc()){
                                    $id_asta=htmlspecialchars($riga["id_asta"],ENT_QUOTES,"UTF-8");
                                    $marca=htmlspecialchars($riga["marca"],ENT_QUOTES,"UTF-8");
                                    $descrizione=htmlspecialchars($riga["descrizione"],ENT_QUOTES,"UTF-8");
                                    $anno=htmlspecialchars($riga["anno"],ENT_QUOTES,"UTF-8");
                                    $prezzo_corrente=htmlspecialchars($riga["prezzo_corrente"],ENT_QUOTES,"UTF-8");

                                    if($asta_selezionata==$riga["id_asta"]){
                                        echo "<option value='$id_asta' selected>";
                                    }else{
                                        echo "<option value='$id_asta'>";
                                    }

                                    echo $marca." ".$anno." - ".$descrizione." - prezzo corrente ".$prezzo_corrente." euro";
                                    echo "</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="importo" class="form-label">Importo offerta</label>
                        <input id="importo" name="importo" type="number" step="0.01" min="1" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Invia offerta</button>
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
