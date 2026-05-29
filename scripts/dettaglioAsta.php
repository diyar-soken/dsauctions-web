<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_GET["id_asta"]) ||
            filter_var($_GET["id_asta"], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>1)))===false){
                throw new Exception("ERRORE, Asta non valida");
        }

        $id_asta=(int)$_GET["id_asta"];

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $sql="SELECT Asta.id_asta, Asta.prezzo_base, Asta.prezzo_corrente, Asta.data_ora_inizio,
                     Asta.data_ora_fine, Asta.stato, Auto.marca, Auto.descrizione, Auto.anno
              FROM Asta INNER JOIN Auto ON Asta.id_auto=Auto.id_auto
              WHERE Asta.id_asta='$id_asta'";
        $risultato=$conn->query($sql);

        if($risultato->num_rows==0){
            throw new Exception("ERRORE, Asta non trovata");
        }

        $asta=$risultato->fetch_assoc();
        $risultato->free();

        $sql="SELECT Offerta.importo, Offerta.data_ora, Utente.username
              FROM Offerta
              INNER JOIN Utente ON Offerta.id_offerente=Utente.id_utente
              WHERE Offerta.id_asta='$id_asta'
              ORDER BY Offerta.importo DESC, Offerta.data_ora DESC";
        $offerte=$conn->query($sql);
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
    <title>dettaglio asta</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <a href="index.php" class="btn btn-link mb-3">Torna alla home</a>

        <?php
            if(isset($_GET['msg'])){
                echo "<div class='alert alert-info'>";
                echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                echo "</div>";
            }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                Dettaglio asta
            </div>
            <div class="card-body">
                <h1 class="h3">
                    <?php
                        echo htmlspecialchars($asta["marca"],ENT_QUOTES,"UTF-8")." ";
                        echo htmlspecialchars($asta["anno"],ENT_QUOTES,"UTF-8");
                    ?>
                </h1>

                <p><?php echo htmlspecialchars($asta["descrizione"],ENT_QUOTES,"UTF-8"); ?></p>

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <div class="border rounded p-3 bg-white">
                            <strong>Prezzo base</strong><br>
                            <?php echo htmlspecialchars($asta["prezzo_base"],ENT_QUOTES,"UTF-8"); ?> euro
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="border rounded p-3 bg-white">
                            <strong>Prezzo corrente</strong><br>
                            <?php echo htmlspecialchars($asta["prezzo_corrente"],ENT_QUOTES,"UTF-8"); ?> euro
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="border rounded p-3 bg-white">
                            <strong>Fine asta</strong><br>
                            <?php echo htmlspecialchars($asta["data_ora_fine"],ENT_QUOTES,"UTF-8"); ?>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="border rounded p-3 bg-white">
                            <strong>Stato</strong><br>
                            <?php echo htmlspecialchars($asta["stato"],ENT_QUOTES,"UTF-8"); ?>
                        </div>
                    </div>
                </div>

                <?php
                    if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"]=="offerente" && $asta["stato"]=="aperta"){
                        echo "<a href='nuovaOfferta.php?id_asta=".htmlspecialchars($asta["id_asta"],ENT_QUOTES,"UTF-8")."' class='btn btn-primary mt-3'>Fai offerta su questa asta</a>";
                    }
                ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Offerte ricevute
            </div>
            <div class="card-body">
                <?php
                    if($offerte->num_rows>0){
                        echo "<table class='table table-bordered table-striped'>";
                        echo "<tr>";
                        echo "<th>utente</th>";
                        echo "<th>importo</th>";
                        echo "<th>data ora</th>";
                        echo "</tr>";

                        while($riga=$offerte->fetch_assoc()){
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($riga["username"],ENT_QUOTES,"UTF-8")."</td>";
                            echo "<td>".htmlspecialchars($riga["importo"],ENT_QUOTES,"UTF-8")." euro</td>";
                            echo "<td>".htmlspecialchars($riga["data_ora"],ENT_QUOTES,"UTF-8")."</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                    }else{
                        echo "<div class='alert alert-warning'>nessuna offerta per questa asta</div>";
                    }

                    $offerte->free();
                    $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html>
