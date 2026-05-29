<?php
    session_start();
    require_once "..\utils\config.php";

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="offerente"){
            throw new Exception("Solo gli offerenti possono vedere questa pagina");
        }

        $id_offerente=(int)$_SESSION["id_utente"];

        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $sql="SELECT Offerta.id_offerta, Offerta.importo, Offerta.data_ora,
                     Asta.id_asta, Asta.stato, Auto.marca, Auto.descrizione
              FROM Offerta
              INNER JOIN Asta ON Offerta.id_asta=Asta.id_asta
              INNER JOIN Auto ON Asta.id_auto=Auto.id_auto
              WHERE Offerta.id_offerente='$id_offerente'
              ORDER BY Offerta.data_ora DESC";
        $risultato=$conn->query($sql);
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
    <title>le mie offerte</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="h3 mb-3 titolo-sezione">Le mie offerte</h1>

        <?php
            if($risultato->num_rows>0){
                echo "<table class='table table-bordered table-striped bg-white'>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>auto</th>";
                echo "<th>asta</th>";
                echo "<th>importo</th>";
                echo "<th>data</th>";
                echo "<th>stato</th>";
                echo "</tr>";

                while($riga=$risultato->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($riga["id_offerta"],ENT_QUOTES,"UTF-8")."</td>";
                    echo "<td>".htmlspecialchars($riga["marca"],ENT_QUOTES,"UTF-8")." - ".htmlspecialchars($riga["descrizione"],ENT_QUOTES,"UTF-8")."</td>";
                    echo "<td>".htmlspecialchars($riga["id_asta"],ENT_QUOTES,"UTF-8")."</td>";
                    echo "<td>".htmlspecialchars($riga["importo"],ENT_QUOTES,"UTF-8")." euro</td>";
                    echo "<td>".htmlspecialchars($riga["data_ora"],ENT_QUOTES,"UTF-8")."</td>";
                    echo "<td>".htmlspecialchars($riga["stato"],ENT_QUOTES,"UTF-8")."</td>";
                    echo "</tr>";
                }

                echo "</table>";
            }else{
                echo "<div class='alert alert-warning'>nessuna offerta effettuata</div>";
            }

            $risultato->free();
            $conn->close();
        ?>

        <a href="index.php" class="btn btn-primary">Torna alla home</a>
    </div>
</body>
</html>
