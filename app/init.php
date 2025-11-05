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
}
require_once 'connect.php';
