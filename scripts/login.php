<?php
    session_start();

    if(isset($_SESSION["id_utente"])){
        header("Location: index.php?msg=".urlencode("Utente già collegato"));
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
    <link rel="icon" href="../imgs/logo.png">
    <title>Accesso</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h4 mb-0">Accesso utente</h1>
                    </div>
                    <div class="card-body">
                        <?php
                            if(isset($_GET['msg'])){
                                echo "<div class='alert alert-warning'>";
                                echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                                echo "</div>";
                            }
                        ?>

                        <form action="elaboraLogin.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input id="username" name="username" type="text" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary">Accedi</button>
                            <button type="reset" class="btn btn-secondary">Azzera</button>
                            <a href="index.php" class="btn btn-link">Torna alla home</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
