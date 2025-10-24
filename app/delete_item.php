<?php
require 'connect.php';
$id = $_GET["item"];
$item_deleted = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn->query("DELETE FROM videojuegos WHERE id=$id");
    $item_deleted = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Item</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container">
    <?php if ($item_deleted): ?>
        <p class="success-msg">Item eliminado ✅ <a href="/items">Volver</a></p>
    <?php else: ?>
        <h3 class="title">¿Seguro que quieres eliminar este item?</h3>
        <form id="item_delete_form" method="POST" class="form-inline">
            <button id="item_delete_submit" type="submit" class="btn btn-danger">Sí, borrar</button>
            <a href="/items" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

