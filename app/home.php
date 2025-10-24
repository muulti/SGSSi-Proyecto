<?php
session_start(); 
if (!isset($_SESSION["user"])) {
    echo "<h3>No has iniciado sesión. <a href='/login.php'>Login</a></h3>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Bienvenido</h2>
        <div class="links">
            <a href="/items">Ver elementos</a><br><br>
            <a href="/modify_user?user=<?php echo htmlspecialchars($_SESSION["user"]); ?>">Modificar mis datos</a>
        </div>
    </div>
</body>
</html>

