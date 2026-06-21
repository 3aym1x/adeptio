<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['admin'])) {
    header('Location: dashboard.php');
} else {
    header('Location: auth/login.php');
}
exit();
