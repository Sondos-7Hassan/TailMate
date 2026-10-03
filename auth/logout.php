<?php require_once __DIR__ . '/../config/config.php';
session_start();

$_SESSION = [];
session_unset();
session_destroy();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

setcookie("email", "", time() - 3600, "/");
setcookie("admin", "", time() - 3600, "/");

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); 
header("Expires: 0"); 

header("Location: " . BASE_URL . "/auth/login.php");
exit;
