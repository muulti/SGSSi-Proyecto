<?php
require_once 'init.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registro</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Registro de usuario</h2>
        <div id="messages"></div>
        <form id="register_form" method="POST" action="register_action.php">
            <input name="nombre" placeholder="Nombre" required>
            <input name="apellidos" placeholder="Apellidos" required>
            <input name="dni" placeholder="11111111-Z" required>
            <input name="telefono" placeholder="Teléfono (9 dígitos)" required>
            <input type="date" name="fecha" required>
            <input type="email" name="email" placeholder="Email" required>
            <input name="usuario" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
	    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <button id="register_submit" type="submit">Registrar</button>
        </form>
        <div class="links"><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></div>
    </div>

    <script src="js/validation.js"></script>
    <script src="js/register.js"></script>

</body>
</html>
