<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,              // session cookie
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']), // only send over HTTPS
        'httponly' => true,           // mitigate XSS
        'samesite' => 'Strict'        // or 'Lax' if you use external redirects
    ]);
    session_start();

    if (empty($_SESSION['csrf_token'])) {
    	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

}
require_once 'connect.php';
