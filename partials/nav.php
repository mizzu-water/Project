<?php if (!isset($prefix)) { $prefix = ''; } ?>
<nav class="navbar navbar-light bg-light border-bottom mb-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">&#9776;</button>
            <span class="navbar-brand mb-0 h1 ms-2">Clinic</span>
        </div>
        <div>
            <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
            <?php if (isset($_SESSION['username'])): ?>
                <span class="navbar-text me-2">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="<?php echo $prefix; ?>auth/logout.php" class="btn btn-secondary btn-sm">Logout</a>
            <?php else: ?>
                <a href="<?php echo $prefix; ?>auth/login.php" class="btn btn-primary btn-sm">Login</a>
            <?php endif; ?>
        </div>
    </div>
  
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Navigation</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="list-group">
            <a href="<?php echo $prefix; ?>index.php" class="list-group-item list-group-item-action">Home</a>
            <a href="<?php echo $prefix; ?>register.php" class="list-group-item list-group-item-action">Register Patient</a>
            <a href="<?php echo $prefix; ?>book_appointment.php" class="list-group-item list-group-item-action">Book Appointment</a>
            <a href="<?php echo $prefix; ?>manage_appointments.php" class="list-group-item list-group-item-action">Appointments</a>
            <a href="<?php echo $prefix; ?>view_bills.php" class="list-group-item list-group-item-action">View Bills</a>
            <a href="<?php echo $prefix; ?>doctor.php" class="list-group-item list-group-item-action">Doctors</a>
            <a href="<?php echo $prefix; ?>schedules.php" class="list-group-item list-group-item-action">Doctor Schedules</a>
            <a href="<?php echo $prefix; ?>index.php" class="list-group-item list-group-item-action">Dashboard</a>
        </div>
    </div>
</div>


