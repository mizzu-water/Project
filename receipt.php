<?php
include 'db.php';

$billId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$bill = null;
if ($billId) {
    $stmt = $conn->prepare("SELECT b.id AS bill_id, b.amount, b.generated_at,
        a.id AS appointment_id, a.appointment_date,
        p.name AS patient_name, p.id AS patient_id,
        d.name AS doctor_name, d.specialty
        FROM bills b
        JOIN appointments a ON b.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE b.id = ?");
    $stmt->bind_param("i", $billId);
    $stmt->execute();
    $bill = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Receipt #<?= htmlspecialchars($billId) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/theme.css" rel="stylesheet">
    <style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff; }
    }
    </style>
</head>
<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>

    <div class="card shadow-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Receipt</h2>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary btn-sm">Print</button>
            </div>
        </div>
        <div class="card-body">
            <?php if (!$bill): ?>
                <div class="alert alert-info">No receipt found.</div>
            <?php else: ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div><strong>Receipt #:</strong> <?= htmlspecialchars($bill['bill_id']) ?></div>
                        <div><strong>Generated:</strong> <?= htmlspecialchars($bill['generated_at']) ?></div>
                        <div><strong>Amount:</strong> $<?= number_format($bill['amount'], 2) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>Patient:</strong> <?= htmlspecialchars($bill['patient_name']) ?> (ID: <?= htmlspecialchars($bill['patient_id']) ?>)</div>
                        <div><strong>Doctor:</strong> <?= htmlspecialchars($bill['doctor_name']) ?> (<?= htmlspecialchars($bill['specialty']) ?>)</div>
                        <div><strong>Appointment:</strong> #<?= htmlspecialchars($bill['appointment_id']) ?> on <?= htmlspecialchars($bill['appointment_date']) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/app.js"></script>
</body>
</html>



