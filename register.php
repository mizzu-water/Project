<?php
include 'db.php';
require_once __DIR__ . '/auth/session.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $contact = trim($_POST['contact']);
    $guardian = trim($_POST['guardian']);

    if ($name && $age > 0 && $contact) {
        $stmt = $conn->prepare("INSERT INTO patients (name, age, contact, guardian_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $name, $age, $contact, $guardian);
        if ($stmt->execute()) {
            $message = "Patient registered successfully. Patient ID: " . $stmt->insert_id;
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Please fill all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Patient Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/theme.css" rel="stylesheet">
    <script src="./assets/js/register.js"></script>
</head>

<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>

    <div class="card">
        <div class="card-header text-center">
            <h2>Patient / Guardian Registration</h2>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form name="regForm" method="post" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label class="form-label">Patient Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Age *</label>
                    <input type="number" name="age" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Info *</label>
                    <input type="text" name="contact" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Guardian Name (if applicable)</label>
                    <input type="text" name="guardian" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
                <a href="index.php" class="btn btn-secondary">Home</a>
            </form>

</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/app.js"></script>