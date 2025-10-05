<?php
require_once __DIR__ . '/session.php';

// Very simple demo credentials. Replace with DB-backed auth later.
$VALID_USERS = [
    'admin' => [ 'password' => 'admin123', 'role' => 'Staff' ],
];

$message = '';

// If already logged in, go home
if (isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $redirect = trim($_POST['redirect'] ?? '../index.php');

    // allow only relative redirects within this app
    if (preg_match('/^https?:/i', $redirect)) { $redirect = '../index.php'; }
    if (strpos($redirect, "\n") !== false || strpos($redirect, "\r") !== false) { $redirect = '../index.php'; }

    if (isset($VALID_USERS[$username]) && hash_equals($VALID_USERS[$username]['password'], $password)) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $VALID_USERS[$username]['role'];
        header('Location: ' . $redirect);
        exit;
    } else {
        $message = 'Invalid username or password.';
    }
}

$redirectParam = isset($_GET['redirect']) ? $_GET['redirect'] : '../index.php';
// Only accept relative redirects for display
if (preg_match('/^https?:/i', $redirectParam)) { $redirectParam = '../index.php'; }
$csrf = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/theme.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-soft">
                <div class="card-header"><h2 class="mb-0">Staff Login</h2></div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectParam) ?>" />
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>


