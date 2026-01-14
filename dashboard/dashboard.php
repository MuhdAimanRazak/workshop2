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

$username = $_SESSION['fullname'] ?? 'User';
$role = $_SESSION['role'] ?? 'staff';

/* =========================
   STAT CARDS (ADMIN)
========================= */
$total_buildings = $conn->query("SELECT COUNT(*) total FROM building")->fetch_assoc()['total'];
$total_rooms = $conn->query("SELECT COUNT(*) total FROM room")->fetch_assoc()['total'];
$total_students = $conn->query("SELECT COUNT(*) total FROM student")->fetch_assoc()['total'];
$total_facilities = $conn->query("SELECT COUNT(*) total FROM facility")->fetch_assoc()['total'];
$maintenance = $conn->query("SELECT COUNT(*) total FROM facility WHERE status!='Available'")->fetch_assoc()['total'];
$maintenanceLabel = ($maintenance == 0) ? 'All Good' : 'Issues';

/* =========================
   ROOM OCCUPANCY
========================= */
$total_beds = $conn->query("SELECT COUNT(*) total FROM room")->fetch_assoc()['total'];
$occupied_beds = $conn->query("SELECT COUNT(DISTINCT room_id) total FROM booking")->fetch_assoc()['total'];
$occupancy = ($total_beds > 0) ? round(($occupied_beds / $total_beds) * 100, 1) : 0;
$displayWidth = max($occupancy, 3);

$barColor = 'bg-success';
if ($occupancy > 70) $barColor = 'bg-danger';
elseif ($occupancy > 30) $barColor = 'bg-warning';

/* =========================
   REPORT SUMMARY
========================= */
$reportStats = [
    'New' => 0,
    'Pending' => 0,
    'Resolved' => 0
];

$qReports = $conn->query("
    SELECT report_status, COUNT(*) total 
    FROM report 
    GROUP BY report_status
");

while ($r = $qReports->fetch_assoc()) {
    if ($r['report_status'] === 'Completed') {
        $reportStats['Resolved'] = $r['total'];
    } else {
        $reportStats[$r['report_status']] = $r['total'];
    }
}

/* =========================
   RECENT REPORTS
========================= */
$qRecent = $conn->query("
    SELECT report_id, report_title, report_status, created_at
    FROM report
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<style>
body { background:#f5f7ff; }
.container-fluid {
    background:#fff;
    border-radius:26px;
    padding:32px;
    box-shadow:0 20px 50px rgba(0,0,0,.08);
}
.welcome-bar {
    background:linear-gradient(135deg,#f6f7ff,#eef2ff);
    border-radius:22px;
    padding:22px 30px;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}
.card.stat {
    border-radius:20px;
    background:linear-gradient(135deg,#8be6b0,#237be7);
    color:#fff;
    box-shadow:0 14px 32px rgba(35,123,231,.25);
}
.card.stat .card-body {
    min-height:140px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}
.card.shadow-sm {
    border-radius:22px;
    box-shadow:0 14px 32px rgba(0,0,0,.08);
}
.progress {
    height:6px;
    border-radius:999px;
    background:#eef2f7;
}
.progress-bar {
    border-radius:999px;
    min-width:12px;
}
</style>

<div class="container-fluid mt-4">

<!-- ===== WELCOME ===== -->
<div class="welcome-bar d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold">
            Welcome, <?= htmlspecialchars($username) ?>
            <span class="badge bg-secondary ms-2"><?= ucfirst($role) ?></span>
        </h4>
        <small id="current-date" class="text-muted"></small>
    </div>
    <div class="text-end">
        <h5 id="current-time" class="fw-bold mb-0"></h5>
        <small class="text-muted">Malaysia Time</small>
    </div>
</div>

<?php if ($role === 'admin') { ?>

<!-- ================= ADMIN DASHBOARD ================= -->

<div class="row g-4 justify-content-center text-center mb-4">
<?php
$stats = [
    ['Total Buildings',$total_buildings,'Registered'],
    ['Total Rooms',$total_rooms,'Active Rooms'],
    ['Total Students',$total_students,'Registered'],
    ['Total Facilities',$total_facilities,'Available'],
    ['Maintenance',$maintenance,$maintenanceLabel]
];
foreach ($stats as $s) {
?>
    <div class="col-md-2">
        <div class="card stat">
            <div class="card-body">
                <h6><?= $s[0] ?></h6>
                <h2><?= $s[1] ?></h2>
                <small><?= $s[2] ?></small>
            </div>
        </div>
    </div>
<?php } ?>
</div>

<div class="row">

    <!-- LEFT -->
    <div class="col-md-8 d-flex flex-column gap-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Room Occupancy</h5>
                <p class="mb-1">Occupied: <?= $occupancy ?>%</p>
                <div class="progress">
                    <div class="progress-bar <?= $barColor ?>" style="width:<?= $displayWidth ?>%"></div>
                </div>
                <small><?= $occupied_beds ?> / <?= $total_beds ?> beds occupied</small>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Recent Reports</h5>
                <ul class="list-group">
                <?php while ($r = $qRecent->fetch_assoc()) { ?>
                    <li class="list-group-item">
                        <a href="../report/report_view.php?id=<?= $r['report_id'] ?>">
                            <?= htmlspecialchars($r['report_title']) ?>
                        </a>
                        <small class="float-end"><?= date("d M Y H:i",strtotime($r['created_at'])) ?></small>
                    </li>
                <?php } ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="col-md-4 d-flex flex-column gap-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Report Summary</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">New <span class="badge bg-primary"><?= $reportStats['New'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between">Pending <span class="badge bg-warning"><?= $reportStats['Pending'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between">Resolved <span class="badge bg-success"><?= $reportStats['Resolved'] ?></span></li>
                </ul>
            </div>
        </div>
    </div>

</div>

<?php } else { ?>

<!-- ================= STAFF DASHBOARD ================= -->

<div class="row mt-4">

    <!-- LEFT COLUMN -->
    <div class="col-md-8 d-flex flex-column gap-4">

        <!-- MY TASKS -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">My Pending Tasks</h5>

                <?php
                $qMyTasks = $conn->query("
                    SELECT report_id, report_title, report_status, created_at
                    FROM report
                    WHERE report_status IN ('New','Pending')
                    ORDER BY created_at DESC
                    LIMIT 5
                ");
                ?>

                <?php if ($qMyTasks->num_rows > 0) { ?>
                <ul class="list-group">
                    <?php while ($r = $qMyTasks->fetch_assoc()) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($r['report_title']) ?>
                            <span class="badge bg-warning"><?= $r['report_status'] ?></span>
                        </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                    <p class="text-muted mb-0">No pending tasks 🎉</p>
                <?php } ?>
            </div>
        </div>

        <!-- RECENT REPORTS -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Recent Reports</h5>

                <ul class="list-group">
                <?php
                mysqli_data_seek($qRecent, 0);
                while ($r = $qRecent->fetch_assoc()) {
                ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($r['report_title']) ?>
                        <small class="float-end text-muted">
                            <?= date("d M Y", strtotime($r['created_at'])) ?>
                        </small>
                    </li>
                <?php } ?>
                </ul>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-md-4 d-flex flex-column gap-4">

        <!-- STAFF INFO -->
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Logged in as</h6>
                <h4 class="fw-bold text-primary"><?= htmlspecialchars($username) ?></h4>
                <span class="badge bg-secondary">Staff</span>
            </div>
        </div>

        <!-- MAIN ACTION -->
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <a href="../report/report_list.php"
                   class="btn btn-primary w-100">
                   View My Reports
                </a>
            </div>
        </div>

    </div>

</div>

<?php } ?>


</div>

<script>
function updateDateTime(){
    const n=new Date();
    document.getElementById('current-date').innerText=
        n.toLocaleDateString('en-MY',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
    document.getElementById('current-time').innerText=
        n.toLocaleTimeString('en-MY',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
setInterval(updateDateTime,1000); updateDateTime();
</script>

<?php include("../page/footer.php"); ?>
