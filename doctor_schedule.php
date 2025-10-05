<?php
include 'db.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/require_staff.php';

$message = '';

// Load doctors for dropdown
$doctors = [];
$res = $conn->query("SELECT id, name FROM doctors ORDER BY name");
if ($res) { while ($row = $res->fetch_assoc()) { $doctors[] = $row; } }

// Handle add schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = intval($_POST['doctor_id']);
    $day = trim($_POST['day']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if ($doctor_id && $day && $start_time && $end_time) {
        $stmt = $conn->prepare("INSERT INTO schedules (doctor_id, day, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $doctor_id, $day, $start_time, $end_time);
        if ($stmt->execute()) {
            $message = "Schedule added.";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Please fill all fields.";
    }
}

// Fetch schedules list
$schedules = [];
$sql = "SELECT s.id, d.name AS doctor_name, s.day, s.start_time, s.end_time
        FROM schedules s JOIN doctors d ON s.doctor_id = d.id
        ORDER BY d.name, FIELD(s.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), s.start_time";
$res2 = $conn->query($sql);
if ($res2) { while ($row = $res2->fetch_assoc()) { $schedules[] = $row; } }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/theme.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>

    <h2>Add Schedule</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Doctor *</label>
                <select name="doctor_id" class="form-select" required>
                    <option value="">-- Select Doctor --</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Day *</label>
                <select name="day" class="form-select" required>
                    <option value="">-- Select Day --</option>
                    <option>Monday</option>
                    <option>Tuesday</option>
                    <option>Wednesday</option>
                    <option>Thursday</option>
                    <option>Friday</option>
                    <option>Saturday</option>
                    <option>Sunday</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Start *</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">End *</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </form>

    <h3>All Schedules</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Doctor</th>
                <th>Day</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedules as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['doctor_name']) ?></td>
                    <td><?= htmlspecialchars($s['day']) ?></td>
                    <td><?= htmlspecialchars(substr($s['start_time'], 0, 5)) ?></td>
                    <td><?= htmlspecialchars(substr($s['end_time'], 0, 5)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
</body>
</html>


