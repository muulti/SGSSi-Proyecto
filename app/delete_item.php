<?php
require 'connect.php';
$id = $_GET["item"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn->query("DELETE FROM videojuegos WHERE id=$id");
    $mensaje_exito = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Juego</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <?php if (isset($mensaje_exito)): ?>
            <div class="alert success">
                <p>✅ Juego eliminado correctamente</p>
            </div>
            <div class="links">
                <a href="/items" class="button">Volver al listado</a>
            </div>
        <?php else: ?>
            <h2>Confirmar Eliminación</h2>
            <div class="alert error">
                <p>⚠️ ¿Estás seguro de que quieres eliminar este juego?</p>
                <p style="font-size: 0.9em; margin-top: 10px;">Esta acción no se puede deshacer.</p>
            </div>
            
            <form id="item_delete_form" method="POST" style="margin-top: 20px;">
                <div class="button-group">
                    <button id="item_delete_submit" type="submit" class="button danger">Sí, eliminar</button>
                    <a href="/items" class="button">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

