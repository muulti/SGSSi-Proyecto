<?php
session_start(); 
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

require_once 'connect.php';

// Obtener el nombre del usuario desde la base de datos
$stmt = $conn->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user"]);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$nombreCompleto = $userData ? htmlspecialchars($userData['nombre'] . ' ' . $userData['apellidos']) : 'Usuario';
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Panel de Control</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>¡Hola <?php echo $nombreCompleto; ?>! Bienvenido/a a tu Panel de Control</h2>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Gestión de Juegos</h3>
                <div class="button-group">
                    <a href="/items" class="button">Ver Catálogo</a>
                    <a href="/items#add" class="button">Añadir Juego</a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3>Mi Cuenta</h3>
                <div class="button-group">
                    <a href="/modify_user?user=<?php echo urlencode($_SESSION["user"]); ?>" class="button">
                        Modificar Mis Datos
                    </a>
                </div>
            </div>
        </div>

        <div class="links">
            <a href="/logout.php" class="button danger">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>
