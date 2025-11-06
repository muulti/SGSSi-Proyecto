<?php
require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die("CSRF validation failed.");
    }
}

$id = $_GET["item"];
$res = $conn->query("SELECT * FROM videojuegos WHERE id=$id");
$item = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE videojuegos SET titulo=?, genero=?, plataforma=?, fecha_lanzamiento=?, precio=? WHERE id=?");
    $stmt->bind_param("ssssdi", $_POST["titulo"], $_POST["genero"], $_POST["plataforma"], $_POST["fecha_lanzamiento"], $_POST["precio"], $id);
    $stmt->execute();
    echo "<p>Item modificado ✅ <a href='/items'>Volver</a></p>";
}
?>
<head>
  <meta charset="UTF-8">
  <title>Modificar Juego</title>
  <link rel="stylesheet" href="css/styles.css">
</head>

<form id="item_modify_form" method="POST">
  <input name="titulo" value="<?= $item['titulo'] ?>"><br>
  <input name="genero" value="<?= $item['genero'] ?>"><br>
  <input name="plataforma" value="<?= $item['plataforma'] ?>"><br>
  <input type="date" name="fecha_lanzamiento" value="<?= $item['fecha_lanzamiento'] ?>"><br>
  <input type="number" step="0.01" name="precio" value="<?= $item['precio'] ?>"><br>
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <button id="item_modify_submit">Actualizar</button>
</form>

