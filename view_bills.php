<?php
include 'db.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/require_staff.php';

$message = '';

// Generate bill for completed appointments without bills
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_bill'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        die('Invalid CSRF token');
    }
    $appointment_id = intval($_POST['generate_bill']);

    // Check if bill already exists
    $stmt = $conn->prepare("SELECT id FROM bills WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $message = "Bill already generated for this appointment.";
    } else {
        // For simplicity, fixed amount
        $amount = 100.00;

        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO bills (appointment_id, amount) VALUES (?, ?)");
        $stmt->bind_param("id", $appointment_id, $amount);
        if ($stmt->execute()) {
            $message = "Bill generated successfully.";
        } else {
            $message = "Error generating bill: " . $conn->error;
        }
    }
    $stmt->close();
}

// Fetch bills with appointment and patient info
$sql = "SELECT b.id AS bill_id, b.amount, b.generated_at, 
        a.id AS appointment_id, a.appointment_date, a.status,
        p.name AS patient_name, d.name AS doctor_name
        FROM bills b
        JOIN appointments a ON b.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        ORDER BY b.generated_at DESC";

$bills = [];
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $bills[] = $row;
    }
}

// Fetch completed appointments without bills for bill generation
$sql2 = "SELECT a.id, p.name AS patient_name, d.name AS doctor_name, a.appointment_date 
         FROM appointments a
         JOIN patients p ON a.patient_id = p.id
         JOIN doctors d ON a.doctor_id = d.id
         WHERE a.status = 'Completed' AND a.id NOT IN (SELECT appointment_id FROM bills)
         ORDER BY a.appointment_date DESC";

$pendingBills = [];
$res2 = $conn->query($sql2);
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        $pendingBills[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/theme.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>
    <h2>Billing Management</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script/app.js"></script>

    <h4>Generate Bill for Completed Appointments</h4>
    <?php if (count($pendingBills) === 0): ?>
        <p>No completed appointments pending billing.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingBills as $appt): ?>
                <tr>
                    <td><?= $appt['id'] ?></td>
                    <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                    <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                    <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                    <td>
                        <form method="post" style="display:inline" onsubmit="return confirm('Generate bill?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(ensureCsrfToken()) ?>">
                            <input type="hidden" name="generate_bill" value="<?= $appt['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-success">Generate Bill</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h4 class="mt-5">View Bills</h4>
    <?php if (count($bills) === 0): ?>
        <p>No bills generated yet.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Appointment Date</th>
                    <th>Amount</th>
                    <th>Generated At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bills as $bill): ?>
                <tr>
                    <td><?= $bill['bill_id'] ?></td>
                    <td><?= $bill['appointment_id'] ?></td>
                    <td><?= htmlspecialchars($bill['patient_name']) ?></td>
                    <td><?= htmlspecialchars($bill['doctor_name']) ?></td>
                    <td><?= htmlspecialchars($bill['appointment_date']) ?></td>
                    <td>$<?= number_format($bill['amount'], 2) ?></td>
                    <td><?= $bill['generated_at'] ?></td>
                    <td>
                        <a href="receipt.php?id=<?= $bill['bill_id'] ?>" class="btn btn-sm btn-primary">Receipt</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>