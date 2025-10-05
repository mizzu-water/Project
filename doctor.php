<?php
include 'db.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/require_staff.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        die('Invalid CSRF token');
    }
    $name = trim($_POST['name']);
    $specialty = trim($_POST['specialty']);
    $contact = trim($_POST['contact']);

    if ($name && $specialty) {
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialty, contact) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $specialty, $contact);
        if ($stmt->execute()) {
            $message = "Doctor added. ID: " . $stmt->insert_id;
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Please enter name and specialty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/theme.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>

    <h2>Add Doctor</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(ensureCsrfToken()) ?>">
        <div class="mb-3">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Specialty *</label>
            <input type="text" name="specialty" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact</label>
            <input type="text" name="contact" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="index.php" class="btn btn-secondary">Home</a>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script/app.js"></script>
</body>
</html>


