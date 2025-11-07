<?php
require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        die("CSRF validation failed.");
    }
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Método no permitido']));
}

if (!isset($_POST['usuario']) || !isset($_POST['password'])) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Faltan campos requeridos']));
}

$usuario = $_POST["usuario"];
$pass = $_POST["password"];

$ip_address = $_SERVER['REMOTE_ADDR'];
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
}

header('Content-Type: application/json');

$check_ip = $conn->prepare("SELECT intentos, bloqueado_hasta FROM login_attempts WHERE ip_address = ?");
$check_ip->bind_param("s", $ip_address);
$check_ip->execute();
$ip_data = $check_ip->get_result()->fetch_assoc();
$check_ip->close();

if ($ip_data && $ip_data['bloqueado_hasta'] !== NULL) {
    $bloqueado_hasta = strtotime($ip_data['bloqueado_hasta']);
    $ahora = time();
    
    if ($ahora < $bloqueado_hasta) {
        $segundos_restantes = $bloqueado_hasta - $ahora;
        echo json_encode([
            'status' => 'error',
            'message' => 'Demasiados intentos fallidos desde esta IP.',
            'bloqueado' => true,
            'tiempo_restante' => $segundos_restantes
        ]);
        exit;
    } else {
        $reset_ip = $conn->prepare("UPDATE login_attempts SET intentos = 0, bloqueado_hasta = NULL WHERE ip_address = ?");
        $reset_ip->bind_param("s", $ip_address);
        $reset_ip->execute();
        $reset_ip->close();
        
        $ip_data['intentos'] = 0;
        $ip_data['bloqueado_hasta'] = NULL;
    }
}

$res = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario=?");
$res->bind_param("s", $usuario);
$res->execute();
$user = $res->get_result()->fetch_assoc();
$res->close();

if ($user && $user['bloqueado_hasta'] !== NULL) {
    $bloqueado_hasta = strtotime($user['bloqueado_hasta']);
    $ahora = time();
    
    if ($ahora < $bloqueado_hasta) {
        $segundos_restantes = $bloqueado_hasta - $ahora;
        echo json_encode([
            'status' => 'error',
            'message' => 'Cuenta bloqueada temporalmente.',
            'bloqueado' => true,
            'tiempo_restante' => $segundos_restantes
        ]);
        exit;
    } else {
        $reset_user = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?");
        $reset_user->bind_param("i", $user['id']);
        $reset_user->execute();
        $reset_user->close();
        $user['intentos_fallidos'] = 0;
        $user['bloqueado_hasta'] = NULL;
    }
}

$login_exitoso = false;

if ($user) {
    $stored = $user['contrasena'];
    if (password_verify($pass, $stored)) {
        $login_exitoso = true;
    } elseif ($pass === $stored) { 
        $login_exitoso = true;
    }
}

if ($login_exitoso) {
    $reset_user = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?");
    $reset_user->bind_param("i", $user['id']);
    $reset_user->execute();
    $reset_user->close();
    
    $reset_ip = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $reset_ip->bind_param("s", $ip_address);
    $reset_ip->execute();
    $reset_ip->close();
    
    $_SESSION["user"] = $user["id"];
    echo json_encode([
        'status' => 'ok',
        'redirect' => 'home.php'
    ]);
    exit;
} else {
    if ($ip_data) {
        $intentos_ip = $ip_data['intentos'] + 1;
    } else {
        $intentos_ip = 1;
    }
    if ($intentos_ip >= 5) {
        $bloqueado_hasta = date('Y-m-d H:i:s', time() + 60);
        if ($ip_data) {
            $update_ip = $conn->prepare("UPDATE login_attempts SET intentos = ?, bloqueado_hasta = ? WHERE ip_address = ?");
            $update_ip->bind_param("iss", $intentos_ip, $bloqueado_hasta, $ip_address);
            $update_ip->execute();
            $update_ip->close();
        } else {
            $insert_ip = $conn->prepare("INSERT INTO login_attempts (ip_address, intentos, bloqueado_hasta) VALUES (?, ?, ?)");
            $insert_ip->bind_param("sis", $ip_address, $intentos_ip, $bloqueado_hasta);
            $insert_ip->execute();
            $insert_ip->close();
        }
        echo json_encode([
            'status' => 'error',
            'message' => 'Has superado el número máximo de intentos. Tu IP ha sido bloqueada.',
            'bloqueado' => true,
            'tiempo_restante' => 60
        ]);
        exit;
    } else {
        // Actualizar contador sin bloquear (intentos 1-4)
        if ($ip_data) {
            $update_ip = $conn->prepare("UPDATE login_attempts SET intentos = ? WHERE ip_address = ?");
            $update_ip->bind_param("is", $intentos_ip, $ip_address);
            $update_ip->execute();
            $update_ip->close();
        } else {
            $insert_ip = $conn->prepare("INSERT INTO login_attempts (ip_address, intentos) VALUES (?, ?)");
            $insert_ip->bind_param("si", $ip_address, $intentos_ip);
            $insert_ip->execute();
            $insert_ip->close();
        }
    }
    
    if ($user) {
        $intentos_user = $user['intentos_fallidos'] + 1;
        
        if ($intentos_user >= 5) {
            $bloqueado_hasta = date('Y-m-d H:i:s', time() + 60);
            $update_user = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id = ?");
            $update_user->bind_param("isi", $intentos_user, $bloqueado_hasta, $user['id']);
            $update_user->execute();
            $update_user->close();
        } else {
            $update_user = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ? WHERE id = ?");
            $update_user->bind_param("ii", $intentos_user, $user['id']);
            $update_user->execute();
            $update_user->close();
        }
    }
    
    $intentos_restantes = 5 - $intentos_ip;
    
    echo json_encode([
        'status' => 'error',
        'message' => "Credenciales incorrectas."
    ]);
    exit;
}
?>
