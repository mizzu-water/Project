<?php
include 'db.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/require_staff.php';

$message = '';

// Fetch patients for dropdown
$patients = [];
$res = $conn->query("SELECT id, name FROM patients ORDER BY name");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $patients[] = $row;
    }
}

// Fetch schedules with doctor info
$schedules = [];
$sql = "SELECT s.id, d.name AS doctor_name, d.specialty, s.day, s.start_time, s.end_time 
        FROM schedules s 
        JOIN doctors d ON s.doctor_id = d.id
        ORDER BY s.day, s.start_time";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $schedules[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        die('Invalid CSRF token');
    }
    $patient_id = intval($_POST['patient_id']);
    $schedule_id = intval($_POST['schedule_id']);
    $appointment_date = $_POST['appointment_date'];

    // Validate inputs
    if ($patient_id && $schedule_id && $appointment_date) {
        // Fetch schedule details once (doctor_id and day)
        $stmt = $conn->prepare("SELECT doctor_id, day FROM schedules WHERE id = ?");
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $stmt->bind_result($doctor_id, $schedule_day);
        $hasSchedule = $stmt->fetch();
        $stmt->close();

        if (!$hasSchedule) {
            $message = "Selected schedule not found.";
        } else {
            $dayOfWeek = date('l', strtotime($appointment_date));
            if ($dayOfWeek !== $schedule_day) {
                $message = "Appointment date does not match the schedule day ($schedule_day).";
            } else {
                // Insert appointment with explicit status so it appears in manage view
                $status = 'Scheduled';
                $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, schedule_id, appointment_date, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiss", $patient_id, $doctor_id, $schedule_id, $appointment_date, $status);
                if ($stmt->execute()) {
                    $message = "Appointment booked successfully. Appointment ID: " . $stmt->insert_id;
                } else {
                    $message = "Error booking appointment: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    } else {
        $message = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/theme.css" rel="stylesheet">
    <script>
    function validateForm() {
        const patient = document.forms["apptForm"]["patient_id"].value;
        const schedule = document.forms["apptForm"]["schedule_id"].value;
        const date = document.forms["apptForm"]["appointment_date"].value;

        if (!patient) {
            alert("Please select a patient.");
            return false;
        }
        if (!schedule) {
            alert("Please select a schedule.");
            return false;
        }
        if (!date) {
            alert("Please select an appointment date.");
            return false;
        }
        return true;
    }
    </script>
</head>
<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>
    <h2>Book Appointment</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form name="apptForm" method="post" onsubmit="return validateForm()">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(ensureCsrfToken()) ?>">
        <div class="mb-3">
            <label class="form-label">Select Patient *</label>
            <select name="patient_id" class="form-select" required>
                <option value="">-- Select Patient --</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Select Doctor Schedule *</label>
            <select name="schedule_id" class="form-select" required>
                <option value="">-- Select Schedule --</option>
                <?php foreach ($schedules as $sch): ?>
                    <option value="<?= $sch['id'] ?>">
                        <?= htmlspecialchars($sch['doctor_name'] . " ({$sch['specialty']}) - {$sch['day']} {$sch['start_time']} to {$sch['end_time']}") ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Appointment Date *</label>
            <input type="date" name="appointment_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Book</button>
        <a href="index.php" class="btn btn-secondary">Home</a>
    </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./script/app.js"></script>
</html>