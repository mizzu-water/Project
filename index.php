<?php
session_start();
require 'db.php';
require_once __DIR__ . '/auth/require_staff.php';

// Fetch statistics
$totalPatients = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc();

$upcomingAppointments = $conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date >= CURDATE() AND status = 'Scheduled'");
$upcomingAppointments->execute();
$upcomingAppointmentsCount = $upcomingAppointments->get_result()->fetch_assoc()['total'];

$completedAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'Completed'")->fetch_assoc()['total'];

$revenue = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM bills");
$revenue->execute();
$revenueSum = $revenue->get_result()->fetch_assoc()['total'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Clinic Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/theme.css" rel="stylesheet">
</head>
<body>
<?php $prefix = ''; include __DIR__ . '/partials/nav.php'; ?>

<div class="container my-4">
    <h1 class="mb-4">Dashboard</h1>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Patients</h5>
                    <p class="card-text fs-2"><?= $totalPatients['total'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Upcoming Appointments</h5>
                    <p class="card-text fs-2"><?=$upcomingAppointmentsCount?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Completed Appointments</h5>
                    <p class="card-text fs-2"><?= $completedAppointments ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Revenue</h5>
                    <p class="card-text fs-2">$<?=number_format($revenueSum, 2)?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card-header mt-5">
        <h5>Patient</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th> Age</th>
                    <th>Contact</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT id, name, age, contact, created_at FROM patients ORDER BY id ASC LIMIT 10");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['id']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['age']) . "</td>
                    <td>" . htmlspecialchars($row['contact']) . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./script/app.js"></script>
</body>
</html>