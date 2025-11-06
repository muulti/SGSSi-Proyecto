<?php
require_once 'init.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
	<h2>Bienvenido a Gamevault<h2>
        <h3>Inicia sesión para ver los juegos</h3>
        <div id="login_messages"></div>
        <form id="login_form" method="POST" action="login_action.php">
	    <input name="usuario" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
	    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <button id="login_submit" type="submit">Entrar</button>
        </form>
        <div class="links">
            <a href="register.php">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
