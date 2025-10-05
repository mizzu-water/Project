<?php
include 'db.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/require_staff.php';

$message = '';

// Handle Cancel
if (isset($_POST['action']) && $_POST['action'] === 'cancel') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { http_response_code(400); die('Invalid CSRF token'); }
    $apptId = intval($_POST['appointment_id']);
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $apptId);
    if ($stmt->execute()) { $message = 'Appointment cancelled.'; } else { $message = 'Error cancelling: ' . $conn->error; }
    $stmt->close();
}

// Handle Reschedule
if (isset($_POST['action']) && $_POST['action'] === 'reschedule') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) { http_response_code(400); die('Invalid CSRF token'); }
    $apptId = intval($_POST['appointment_id']);
    $newDate = $_POST['new_date'];

    // Ensure date matches schedule day for this appointment
    $stmt = $conn->prepare("SELECT s.day FROM appointments a JOIN schedules s ON a.schedule_id = s.id WHERE a.id = ?");
    $stmt->bind_param("i", $apptId);
    $stmt->execute();
    $stmt->bind_result($scheduleDay);
    $stmt->fetch();
    $stmt->close();

    if (!$newDate) {
        $message = 'Please select a new date.';
    } else if (date('l', strtotime($newDate)) !== $scheduleDay) {
        $message = "Selected date does not match schedule day ($scheduleDay).";
    } else {
        $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, status = 'Rescheduled' WHERE id = ?");
        $stmt->bind_param("si", $newDate, $apptId);
        if ($stmt->execute()) { $message = 'Appointment rescheduled.'; } else { $message = 'Error rescheduling: ' . $conn->error; }
        $stmt->close();
    }
}

// Fetch upcoming and active appointments
$appts = [];
$sql = "SELECT a.id, a.appointment_date, a.status, p.name AS patient_name, d.name AS doctor_name, d.specialty
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.status IN ('Scheduled','Rescheduled')
        ORDER BY a.appointment_date ASC";
$res = $conn->query($sql);
if ($res) { while ($row = $res->fetch_assoc()) { $appts[] = $row; } }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/theme.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>

    <div class="card shadow-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Appointments</h2>
            <a href="book_appointment.php" class="btn btn-primary btn-sm">Book New</a>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-info mb-3"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if (count($appts) === 0): ?>
                <p class="text-muted mb-0">No scheduled appointments.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Specialty</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th style="width: 260px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appts as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td><?= htmlspecialchars($a['patient_name']) ?></td>
                            <td><?= htmlspecialchars($a['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($a['specialty']) ?></td>
                            <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($a['status']) ?></td>
                            <td>
                                <form method="post" class="d-inline-flex align-items-center gap-2">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(ensureCsrfToken()) ?>">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <input type="date" name="new_date" class="form-control form-control-sm" />
                                    <button name="action" value="reschedule" class="btn btn-sm btn-info">Reschedule</button>
                                </form>
                                <form method="post" class="d-inline" onsubmit="return confirm('Cancel this appointment?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(ensureCsrfToken()) ?>">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <button name="action" value="cancel" class="btn btn-sm btn-secondary">Cancel</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script/app.js"></script>
</body>
</html>


