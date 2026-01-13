<?php
include("../config/config.php");
include("../page/header.php");

/* =========================
   STAT CARDS
========================= */
$total_buildings = $conn->query("SELECT COUNT(*) total FROM building")->fetch_assoc()['total'];

$total_rooms = $conn->query("
    SELECT COUNT(DISTINCT no_house, room_no) total
    FROM room
")->fetch_assoc()['total'];

$total_students = $conn->query("SELECT COUNT(*) total FROM student")->fetch_assoc()['total'];

$total_facilities = $conn->query("SELECT COUNT(*) total FROM facility")->fetch_assoc()['total'];

$maintenance = $conn->query("
    SELECT COUNT(*) total FROM facility WHERE status != 'Available'
")->fetch_assoc()['total'];

$maintenanceLabel = ($maintenance == 0) ? 'All Good' : 'Facilities';


/* =========================
   ROOM OCCUPANCY
========================= */
$total_beds = $conn->query("SELECT COUNT(*) total FROM room")->fetch_assoc()['total'];

$occupied_beds = $conn->query("
    SELECT COUNT(DISTINCT room_id) total FROM booking
")->fetch_assoc()['total'];

$occupancy = ($total_beds > 0)
    ? round(($occupied_beds / $total_beds) * 100, 1)
    : 0;

// Minimum visual width
$displayWidth = max($occupancy, 3);

// Color logic
$barColor = 'bg-success';
if ($occupancy > 70) $barColor = 'bg-danger';
elseif ($occupancy > 30) $barColor = 'bg-warning';


/* =========================
   REPORT SUMMARY
========================= */
$reportStats = ['New'=>0,'Pending'=>0,'Resolved'=>0];

$qReports = $conn->query("
    SELECT report_status, COUNT(*) total
    FROM report
    GROUP BY report_status
");

while ($r = $qReports->fetch_assoc()) {
    if (isset($reportStats[$r['report_status']])) {
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
.card h2 { line-height:1.2; }
.card small { opacity:.85; }
.list-group-item { cursor:pointer; }
.list-group-item a:hover { text-decoration:underline; }
</style>

<div class="container-fluid mt-4">

<!-- ================= STAT CARDS ================= -->
<div class="row g-3">
    <div class="col-md-2">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <h6>Total Buildings</h6>
                <h2><?= $total_buildings ?></h2>
                <small>Registered</small>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <h6>Total Rooms</h6>
                <h2><?= $total_rooms ?></h2>
                <small>Active Rooms</small>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <h6>Total Students</h6>
                <h2><?= $total_students ?></h2>
                <small>Registered</small>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card bg-secondary text-white shadow-sm">
            <div class="card-body">
                <h6>Total Facilities</h6>
                <h2><?= $total_facilities ?></h2>
                <small>Available</small>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body">
                <h6>Maintenance</h6>
                <h2><?= $maintenance ?></h2>
                <small><?= $maintenanceLabel ?></small>
            </div>
        </div>
    </div>
</div>

<!-- ================= OCCUPANCY + ACTIONS ================= -->
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Room Occupancy</h5>
                <p>Occupied: <?= $occupancy ?>%</p>
                <div class="progress mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated <?= $barColor ?>"
                         style="width: <?= $displayWidth ?>%"
                         role="progressbar"
                         aria-valuenow="<?= $occupancy ?>"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted">
                    <?= $occupied_beds ?> / <?= $total_beds ?> beds occupied
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Quick Actions</h5>
                <a href="add_building.php" class="btn btn-primary w-100 mb-2">Add Building</a>
                <a href="add_room.php" class="btn btn-info w-100 mb-2">Add Room</a>
                <a href="add_facility.php" class="btn btn-success w-100 mb-2">Add Facility</a>
                <a href="report.php" class="btn btn-outline-secondary w-100">View Reports</a>
            </div>
        </div>
    </div>
</div>

<!-- ================= REPORT SUMMARY + RECENT ================= -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Report Summary</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="report.php?status=New">🟦 New</a>
                        <span class="badge bg-primary"><?= $reportStats['New'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="report.php?status=Pending">🟨 Pending</a>
                        <span class="badge bg-warning"><?= $reportStats['Pending'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="report.php?status=Resolved">🟩 Resolved</a>
                        <span class="badge bg-success"><?= $reportStats['Resolved'] ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Recent Reports</h5>

                <?php if ($qRecent->num_rows > 0) { ?>
                <ul class="list-group">
                    <?php while ($r = $qRecent->fetch_assoc()) {
                        $dot = match($r['report_status']) {
                            'New' => 'bg-primary',
                            'Pending' => 'bg-warning',
                            'Resolved' => 'bg-success',
                            default => 'bg-secondary'
                        };
                        $style = ($r['report_status']=='New') ? 'fw-bold' : 'text-muted';
                    ?>
                    <li class="list-group-item <?= $style ?>">
                        <span class="badge rounded-pill <?= $dot ?> me-2">&nbsp;</span>
                        <a href="report_view.php?id=<?= $r['report_id'] ?>"
                           class="text-decoration-none">
                           <?= htmlspecialchars($r['report_title']) ?>
                        </a>
                        <small class="float-end">
                            <?= date("d M Y H:i", strtotime($r['created_at'])) ?>
                        </small>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                    <p class="text-muted text-center py-3">No recent reports</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

</div>

<?php include("../page/footer.php"); ?>
