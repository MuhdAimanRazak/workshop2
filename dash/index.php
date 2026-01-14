<?php
session_start();
include("../config/config.php");
include("../page/header.php");

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['staff_ic'])) {
    header("Location: ../log/index.php");
    exit();
}

$role = $_SESSION['role'] ?? 'staff';
?>

<main>
<style>
    li { list-style: none; }
    .card-link { text-decoration: none; }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">
            <?= ucfirst($role) ?> Dashboard
        </li>
    </ol>

    <div class="row">

        <!-- ================= STAFF CARD ================= -->
        <?php if ($role === 'admin' || $role === 'staff') { ?>
        <div class="col-xl-3 col-md-6">
            <a href="../staff/staff_section.php" class="card-link">
                <div class="card bg-purple text-white mb-4">
                    <div class="card-body">STAFF</div>
                </div>
            </a>
        </div>
        <?php } ?>

        <!-- ================= ROOM CARD ================= -->
        <?php if ($role === 'admin' || $role === 'staff') { ?>
        <div class="col-xl-3 col-md-6">
            <a href="../room/room_section.php" class="card-link">
                <div class="card bg-purple text-white mb-4">
                    <div class="card-body">ROOM</div>
                </div>
            </a>
        </div>
        <?php } ?>

        <!-- ================= STUDENT CARD ================= -->
        <?php if ($role === 'admin' || $role === 'staff') { ?>
        <div class="col-xl-3 col-md-6">
            <a href="../students/student_section.php" class="card-link">
                <div class="card bg-purple text-white mb-4">
                    <div class="card-body">STUDENT</div>
                </div>
            </a>
        </div>
        <?php } ?>

        <!-- ================= FACILITIES CARD ================= -->
        <?php if ($role === 'admin' || $role === 'staff') { ?>
        <div class="col-xl-3 col-md-6">
            <a href="../facility/facility.php" class="card-link">
                <div class="card bg-purple text-white mb-4">
                    <div class="card-body">FACILITIES</div>
                </div>
            </a>
        </div>
        <?php } ?>

        <!-- ================= REPORT CARD ================= -->
        <?php if ($role === 'admin' || $role === 'staff') { ?>
        <div class="col-xl-3 col-md-6">
            <a href="../report/report_list.php" class="card-link">
                <div class="card bg-purple text-white mb-4">
                    <div class="card-body">REPORT</div>
                </div>
            </a>
        </div>
        <?php } ?>

        <!-- ================= ADMIN ONLY ================= -->
        <?php if ($role === 'admin') { ?>
        <div class="col-xl-3 col-md-6">
            <a href="../reservation/reservation_section.php" class="card-link">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">RESERVATION</div>
                </div>
            </a>
        </div>
        <?php } ?>

    </div>

    <!-- ADMIN ONLY SECTION -->
    <?php if ($role === 'admin') { ?>
    <div class="row">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                New Reservation that need action
            </div>
            <div class="card-body">
                <!-- admin-only table -->
            </div>
        </div>
    </div>
    <?php } ?>

</div>
</main>

<?php include("../page/footer.php"); ?>
