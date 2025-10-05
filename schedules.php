<?php
include 'db.php';

$schedules = [];
$sql = "SELECT s.id, d.name AS doctor_name, d.specialty, s.day, s.start_time, s.end_time 
        FROM schedules s 
        JOIN doctors d ON s.doctor_id = d.id
        ORDER BY FIELD(s.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), s.start_time";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) { $schedules[] = $row; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/theme.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Staff'): ?>
        <?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>
    <?php endif; ?>

    <div class="card shadow-soft">
        <div class="card-header"><h2 class="mb-0">Doctor Schedules</h2></div>
        <div class="card-body">
            <?php if (count($schedules) === 0): ?>
                <p class="text-muted mb-0">No schedules found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialty</th>
                                <th>Day</th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $sch): ?>
                            <tr>
                                <td><?= htmlspecialchars($sch['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($sch['specialty']) ?></td>
                                <td><?= htmlspecialchars($sch['day']) ?></td>
                                <td><?= htmlspecialchars(substr($sch['start_time'], 0, 5)) ?></td>
                                <td><?= htmlspecialchars(substr($sch['end_time'], 0, 5)) ?></td>
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


