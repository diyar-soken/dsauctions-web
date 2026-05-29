<?php
    session_start();

    try{
        if(!isset($_SESSION["id_utente"])){
            header("Location: login.php?msg=".urlencode("Devi effettuare l'accesso"));
            exit;
        }

        if($_SESSION["ruolo"]!="amministratore"){
            throw new Exception("Solo gli amministratori possono inserire auto");
        }
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
    <title>nuova auto</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1 class="h4 mb-0">Inserimento nuova auto</h1>
            </div>
            <div class="card-body">
                <?php
                    if(isset($_GET['msg'])){
                        echo "<div class='alert alert-warning'>";
                        echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                        echo "</div>";
                    }
                ?>

                <form action="elaboraAuto.php" method="POST">
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input id="marca" name="marca" type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <input id="descrizione" name="descrizione" type="text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="anno" class="form-label">Anno</label>
                        <input id="anno" name="anno" type="number" min="1950" max="2030" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Inserisci</button>
                    <button type="reset" class="btn btn-secondary">Azzera</button>
                    <a href="index.php" class="btn btn-link">Torna alla home</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
