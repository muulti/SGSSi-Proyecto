<?php
require 'connect.php';

$mensaje="";
$tipo_alerta="";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (empty($_POST["titulo"]) || empty($_POST["genero"]) || empty($_POST["plataforma"]) || empty($_POST["fecha_lanzamiento"]) || empty($_POST["precio"])) {
    $mensaje="¡Todos los campos son obligatorios!";
  }else{
    $stmt = $conn->prepare("INSERT INTO videojuegos (titulo, genero, plataforma, fecha_lanzamiento, precio) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssd", $_POST["titulo"], $_POST["genero"], $_POST["plataforma"], $_POST["fecha_lanzamiento"], $_POST["precio"]);
    if($stmt->execute()){
      $mensaje="Juego añadido correctamente!";
      $tipo_alerta="success";
    }
    else{
      $mensaje="Error al añadir el juego: ".$stmt->error;
      $tipo_alerta="error";
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Añadir Juego</title>
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>
  <div class="container">
    <h2>Añadir nuevo juego</h2>

    <?php if ($mensaje): ?>
        <div class="alert <?= htmlspecialchars($tipo_alerta) ?>">
            <?= $mensaje ?> 
        </div>
    <?php endif; ?>
    
    <?php 

    if ($tipo_alerta != "success"): 
    ?>

    <form id="item_add_form" method="POST">
      <input name="titulo" placeholder="Título" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"><br>
      <input name="genero" placeholder="Género" value="<?= htmlspecialchars($_POST['genero'] ?? '') ?>"><br>
      <input name="plataforma" placeholder="Plataforma" value="<?= htmlspecialchars($_POST['plataforma'] ?? '') ?>"><br>
      <input type="date" name="fecha_lanzamiento" value="<?= htmlspecialchars($_POST['fecha_lanzamiento'] ?? '') ?>"><br>
      <input type="number" step="0.01" name="precio" placeholder="Precio" value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>"><br>
      <button id="item_add_submit">Guardar</button>
    </form>
    
    <?php 
    endif; 
    ?>

    <?php if ($tipo_alerta != "success"): ?>
    <div class="links">
        <a href="/">Ir a Inicio</a> | <a href="/items">Volver a la lista</a>
    </div>
    <?php else: ?>
    <div class="links">
        <a href="/items" class="button">Ver listado de juegos</a>
    </div>
    <?php endif; ?>

  </div>
</body>
</html>

