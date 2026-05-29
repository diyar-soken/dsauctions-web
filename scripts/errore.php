<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/stile.css">
    <title>errore</title>
    <link rel="icon" href="../imgs/logo.png">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h1 class="h4 mb-0">pagina di errore</h1>
            </div>
            <div class="card-body">
                <?php
                    if(isset($_GET['msg'])){
                        echo "<div class='alert alert-danger'>";
                        echo htmlspecialchars($_GET['msg'],ENT_QUOTES,"UTF-8");
                        echo "</div>";
                    }
                ?>
                <a href="index.php" class="btn btn-primary">Torna alla home</a>
            </div>
        </div>
    </div>
</body>
</html>
