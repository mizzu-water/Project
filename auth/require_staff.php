<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    $base = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/');
    if ($base === '' || $base === '\\') { $base = '/'; }
    header('Location: ' . $base . '/auth/login.php?redirect=' . $redirect);
    exit;
}
?>


