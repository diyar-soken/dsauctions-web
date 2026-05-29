<?php
    session_start();
    require_once "..\utils\config.php";

    function immagineAuto($marca){
        $marca=strtolower($marca);

        if(strpos($marca,"bmw")!==false){
            return "https://commons.wikimedia.org/wiki/Special:FilePath/BMW%20M5%20%28F90%29%201X7A6242.jpg?width=700";
        }

        if(strpos($marca,"porsche")!==false){
            return "https://commons.wikimedia.org/wiki/Special:FilePath/Porsche%20911%20992.jpg?width=700";
        }

        if(strpos($marca,"audi")!==false){
            return "https://commons.wikimedia.org/wiki/Special:FilePath/Audi%20RS6%20Avant%20C8%201X7A0305.jpg?width=700";
        }

        if(strpos($marca,"fiat")!==false){
            return "https://commons.wikimedia.org/wiki/Special:FilePath/Fiat%20Panda%20%282020%29%20%2853984689915%29.jpg?width=700";
        }

        if(strpos($marca,"volkswagen")!==false){
            return "https://commons.wikimedia.org/wiki/Special:FilePath/2019%20Volkswagen%20Golf%20VII.jpg?width=700";
        }

        return "https://commons.wikimedia.org/wiki/Special:FilePath/Porsche%20911%20992.jpg?width=700";
    }

    $marca_ricerca="";
    $prezzo_massimo="";
    $num_aste=0;
    $num_auto=0;
    $num_offerte=0;

    try{
        $conn=new mysqli($dati_conn["host"],$dati_conn["user"],$dati_conn["psw"],$dati_conn["db"]);

        $where="WHERE Asta.stato='aperta'
                    AND Asta.data_ora_inizio<=NOW()
                    AND Asta.data_ora_fine>=NOW()";

        if(isset($_GET["marca"]) && strlen(trim($_GET["marca"]))>0){
            $marca_ricerca=trim($_GET["marca"]);
            $marca_sql=$conn->real_escape_string($marca_ricerca);
            $where.=" AND Auto.marca LIKE '%$marca_sql%'";
        }

        if(isset($_GET["prezzo_massimo"]) && strlen($_GET["prezzo_massimo"])>0){
            if(filter_var($_GET["prezzo_massimo"], FILTER_VALIDATE_FLOAT)===false){
                throw new Exception("ERRORE, Prezzo massimo non valido");
            }

            $prezzo_massimo=$_GET["prezzo_massimo"];
            $prezzo_massimo_sql=$conn->real_escape_string($prezzo_massimo);
            $where.=" AND Asta.prezzo_corrente<='$prezzo_massimo_sql'";
        }

        $sql="SELECT Asta.id_asta, Asta.prezzo_base, Asta.prezzo_corrente, Asta.data_ora_fine, Asta.stato,
                     Auto.marca, Auto.descrizione, Auto.anno
              FROM Asta INNER JOIN Auto ON Asta.id_auto=Auto.id_auto
              $where
              ORDER BY Asta.data_ora_fine ASC";

        $risultato=$conn->query($sql);

        $stat=$conn->query("SELECT COUNT(*) AS totale FROM Asta WHERE stato='aperta'");
        $riga=$stat->fetch_assoc();
        $num_aste=$riga["totale"];
        $stat->free();

        $stat=$conn->query("SELECT COUNT(*) AS totale FROM Auto");
        $riga=$stat->fetch_assoc();
        $num_auto=$riga["totale"];
        $stat->free();

        $stat=$conn->query("SELECT COUNT(*) AS totale FROM Offerta");
        $riga=$stat->fetch_assoc();
        $num_offerte=$riga["totale"];
        $stat->free();
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
    <title>DSAuctions</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg" data-bs-theme="dark" style="background-color: #b5b5b5;">
<div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../imgs/logo.png" class="logo-navbar" alt="logo DSAuctions">
            </a>

            <div class="d-flex gap-2">
                <?php
                    if(isset($_SESSION["id_utente"])){
                        echo "<span class='navbar-text me-2'>";
                        echo htmlspecialchars($_SESSION["username"],ENT_QUOTES,"UTF-8");
                        echo " - ";
                        echo htmlspecialchars($_SESSION["ruolo"],ENT_QUOTES,"UTF-8");
                        echo "</span>";

                        echo "<form action='logout.php' method='POST'>";
                        echo "<button type='submit' class='btn btn-outline-light btn-sm'>Esci</button>";
                        echo "</form>";
                    }else{
                        echo "<a href='login.php' class='btn btn-outline-light btn-sm'>Accedi</a>";
                        echo "<a href='register.php' class='btn btn-warning btn-sm'>Registrati</a>";
                    }
                ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row align-items-center border rounded p-4 mb-4 hero-box">
            <div class="col-md-8">
                <h1 class="display-6 titolo-sezione">Aste online di automobili</h1>
                <p class="lead mb-0 testo-morbido">Trova un'auto, consulta i dettagli dell'asta e fai la tua offerta in modo semplice e sicuro.</p>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <strong><?php echo htmlspecialchars($num_aste,ENT_QUOTES,"UTF-8"); ?></strong>
                            <br>
                            <small>Aste aperte</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <strong><?php echo htmlspecialchars($num_auto,ENT_QUOTES,"UTF-8"); ?></strong>
                            <br>
                            <small>Auto</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <strong><?php echo htmlspecialchars($num_offerte,ENT_QUOTES,"UTF-8"); ?></strong>
                            <br>
                            <small>Offerte</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            if(isset($_GET['msg'])){
                echo "<div class='alert alert-info'>";
                echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                echo "</div>";
            }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                Ricerca aste
            </div>
            <div class="card-body">
                <form action="index.php" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label for="marca" class="form-label">Marca</label>
                        <input id="marca" name="marca" type="text" class="form-control" value="<?php echo htmlspecialchars($marca_ricerca,ENT_QUOTES,'UTF-8'); ?>" placeholder="BMW, Porsche, Audi">
                    </div>

                    <div class="col-md-4">
                        <label for="prezzo_massimo" class="form-label">Prezzo massimo</label>
                        <input id="prezzo_massimo" name="prezzo_massimo" type="number" step="0.01" min="1" class="form-control" value="<?php echo htmlspecialchars($prezzo_massimo,ENT_QUOTES,'UTF-8'); ?>">
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">Cerca</button>
                        <a href="index.php" class="btn btn-secondary w-100">Azzera</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <?php
                if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"]=="offerente"){
                    echo "<div class='col-md-3 mb-2'><a href='nuovaOfferta.php' class='btn btn-primary w-100'>Nuova offerta</a></div>";
                    echo "<div class='col-md-3 mb-2'><a href='mieOfferte.php' class='btn btn-outline-primary w-100'>Le mie offerte</a></div>";
                }

                if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"]=="amministratore"){
                    echo "<div class='col-md-3 mb-2'><a href='nuovaAuto.php' class='btn btn-primary w-100'>Nuova auto</a></div>";
                    echo "<div class='col-md-3 mb-2'><a href='nuovaAsta.php' class='btn btn-outline-primary w-100'>Nuova asta</a></div>";
                    echo "<div class='col-md-3 mb-2'><a href='visualizzaOfferte.php' class='btn btn-outline-dark w-100'>Offerte ricevute</a></div>";
                }
            ?>
        </div>

        <h2 class="h4 mb-3 titolo-sezione">Aste disponibili</h2>

        <?php
            if($risultato->num_rows>0){
                echo "<div class='row'>";

                while($riga=$risultato->fetch_assoc()){
                    $id_asta=htmlspecialchars($riga["id_asta"],ENT_QUOTES,"UTF-8");
                    $marca=htmlspecialchars($riga["marca"],ENT_QUOTES,"UTF-8");
                    $descrizione=htmlspecialchars($riga["descrizione"],ENT_QUOTES,"UTF-8");
                    $anno=htmlspecialchars($riga["anno"],ENT_QUOTES,"UTF-8");
                    $prezzo_base=htmlspecialchars($riga["prezzo_base"],ENT_QUOTES,"UTF-8");
                    $prezzo_corrente=htmlspecialchars($riga["prezzo_corrente"],ENT_QUOTES,"UTF-8");
                    $data_ora_fine=htmlspecialchars($riga["data_ora_fine"],ENT_QUOTES,"UTF-8");
                    $immagine=htmlspecialchars(immagineAuto($riga["marca"]),ENT_QUOTES,"UTF-8");

                    echo "<div class='col-md-6 col-lg-4 mb-4'>";
                    echo "<div class='card h-100'>";
                    echo "<img src='$immagine' class='card-img-top' style='height:190px; object-fit:cover' alt='auto'>";
                    echo "<div class='card-body'>";
                    echo "<h3 class='h5'>".$marca." ".$anno."</h3>";
                    echo "<p>".$descrizione."</p>";
                    echo "<p class='mb-1'><strong>Prezzo base:</strong> ".$prezzo_base." euro</p>";
                    echo "<p class='mb-1'><strong>Prezzo corrente:</strong> <span class='prezzo'>".$prezzo_corrente." euro</span></p>";
                    echo "<p><strong>Fine asta:</strong> ".$data_ora_fine."</p>";
                    echo "<a href='dettaglioAsta.php?id_asta=$id_asta' class='btn btn-outline-primary me-2'>Dettagli</a>";

                    if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"]=="offerente"){
                        echo "<a href='nuovaOfferta.php?id_asta=$id_asta' class='btn btn-primary'>Fai offerta</a>";
                    }

                    if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"]=="amministratore"){
                        echo "<form action='chiudiAsta.php' method='POST' class='d-inline'>";
                        echo "<input type='hidden' name='id_asta' value='$id_asta'>";
                        echo "<button type='submit' class='btn btn-danger'>Chiudi</button>";
                        echo "</form>";
                    }

                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }

                echo "</div>";
            }else{
                echo "<div class='alert alert-warning'>Nessuna asta trovata</div>";
            }

            $risultato->free();
            $conn->close();
        ?>
    </div>
</body>
</html>
