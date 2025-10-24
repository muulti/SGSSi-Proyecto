<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

require 'connect.php';
$id = $_SESSION["user"];

// Usar consulta preparada para obtener los datos del usuario
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error al obtener los datos del usuario: " . $stmt->error);
}

$user = $result->fetch_assoc();
if (!$user) {
    die("No se encontró el usuario con ID: " . htmlspecialchars($id));
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellidos = trim($_POST["apellidos"]);
    $dni = trim($_POST["dni"]);
    $telefono = trim($_POST["telefono"]);
    $fecha_nacimiento = trim($_POST["fecha_nacimiento"]);
    $email = trim($_POST["email"]);
    $nombre_usuario = trim($_POST["nombre_usuario"]);
    $contrasena = $_POST["contrasena"] ?? null;
    
    // Validaciones del lado del servidor
    $errors = [];
    
    // Validar nombre
    if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ '´`.-]{2,100}$/", $nombre)) {
        $errors[] = "Nombre no válido. Solo letras y espacios, 2-100 caracteres.";
    }
    
    // Validar apellidos
    if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ '´`.-]{2,100}$/", $apellidos)) {
        $errors[] = "Apellidos no válidos. Solo letras y espacios, 2-100 caracteres.";
    }
    
    // Validar DNI
    if (!preg_match("/^\\d{8}-[A-Z]$/", $dni)) {
        $errors[] = "Formato DNI incorrecto (11111111-Z)";
    } else {
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $numero = intval(substr($dni, 0, 8));
        $letra = $dni[9];
        if ($letras[$numero % 23] !== $letra) {
            $errors[] = "La letra del DNI no corresponde";
        }
    }
    
    // Validar teléfono
    if (!preg_match("/^\\d{9}$/", $telefono)) {
        $errors[] = "El teléfono debe tener 9 dígitos";
    }
    
    // Validar fecha (formato YYYY-MM-DD)
    if (empty($fecha_nacimiento)) {
        $errors[] = "La fecha de nacimiento es obligatoria";
    } elseif (!preg_match("/^\\d{4}-\\d{2}-\\d{2}$/", $fecha_nacimiento)) {
        $errors[] = "Formato de fecha no válido (debe ser YYYY-MM-DD)";
    } elseif (!strtotime($fecha_nacimiento)) {
        $errors[] = "Fecha no válida";
    } else {
        $fechaTimestamp = strtotime($fecha_nacimiento);
        if ($fechaTimestamp > time()) {
            $errors[] = "La fecha de nacimiento no puede ser futura";
        }
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email no válido";
    }
    
    // Validar usuario
    if (!preg_match("/^[A-Za-z0-9_\\-]{3,50}$/", $nombre_usuario)) {
        $errors[] = "Nombre de usuario no válido. Solo letras, números, _ o - y 3-50 caracteres.";
    }
    
    // Validar contraseña si se proporcionó una nueva
    if ($contrasena !== null && $contrasena !== '') {
        if (strlen($contrasena) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }
    }
    
    // Verificar si el DNI o usuario ya existe para otro usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE (dni = ? OR nombre_usuario = ?) AND id != ?");
    $stmt->bind_param("ssi", $dni, $nombre_usuario, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "El DNI o nombre de usuario ya está en uso por otro usuario";
    }
    $stmt->close();
    
    // Si hay errores, mostrarlos
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }
    
    // Preparar la consulta de actualización
    if ($contrasena !== null && $contrasena !== '') {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellidos=?, dni=?, telefono=?, fecha_nacimiento=?, email=?, nombre_usuario=?, contrasena=? WHERE id=?");
        $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt->bind_param("ssssssssi", $nombre, $apellidos, $dni, $telefono, $fecha_nacimiento, $email, $nombre_usuario, $hashedPassword, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellidos=?, dni=?, telefono=?, fecha_nacimiento=?, email=?, nombre_usuario=? WHERE id=?");
        $stmt->bind_param("sssssssi", $nombre, $apellidos, $dni, $telefono, $fecha_nacimiento, $email, $nombre_usuario, $id);
    }
    
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar los datos: ' . $stmt->error]);
        exit;
    }
    
    $stmt->close();
    echo json_encode(['status' => 'ok', 'message' => 'Datos actualizados correctamente']);
    exit;
}

// Mensaje de éxito
if (isset($_GET['updated'])) {
    echo "<div class='alert success'>Datos actualizados correctamente ✅</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Datos - <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Modificar Mis Datos</h2>
        <div id="messages"></div>
        <form id="user_modify_form" method="POST" class="form">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($user['apellidos']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($user['dni']); ?>" required placeholder="11111111-Z">
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($user['fecha_nacimiento']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($user['nombre_usuario']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="contrasena">Nueva Contraseña (dejar en blanco para mantener la actual):</label>
                <input type="password" id="contrasena" name="contrasena" minlength="6">
            </div>
            
            <div class="button-group">
                <button type="submit" id="user_modify_submit" class="button">Actualizar Datos</button>
                <a href="/home" class="button">Volver al Panel</a>
            </div>
        </form>
    </div>

    <script src="js/validation.js"></script>
    <script>
        // Función de validación específica para modificación
        function validarModificacion() {
            try {
                return validarRegistro();
            } catch (error) {
                console.error('Error en la validación:', error);
                return false;
            }
        }

        document.getElementById('user_modify_form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            
            try {
                if (!validarModificacion()) return;
                
                const formData = new FormData(this);
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status === 'ok') {
                    messagesDiv.innerHTML = '<div class="alert success">' + result.message + '</div>';
                    setTimeout(() => window.location.href = '/home', 2000);
                } else {
                    const errorMessage = result.errors ? result.errors.join('<br>') : result.message;
                    messagesDiv.innerHTML = '<div class="alert error">' + errorMessage + '</div>';
                }
            } catch (error) {
                console.error('Error:', error);
                messagesDiv.innerHTML = '<div class="alert error">Error al procesar la solicitud. Por favor, inténtalo de nuevo.</div>';
            }
        });
    </script>
</body>
</html>
