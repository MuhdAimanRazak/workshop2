<?php
include("../page/header.php");
include("../config/config.php");

// =========================
// STATUS ENUM MAPPING
// UI <-> DATABASE
// =========================
$statusUiToDb = [
    'New'      => 'New',
    'Pending'  => 'In Progress',
    'Resolved' => 'Completed'
];

$statusDbToUi = [
    'New'         => 'New',
    'In Progress' => 'Pending',
    'Completed'   => 'Resolved'
];

if (!$conn) {
    die("<div class='alert alert-danger'>Database connection failed</div>");
}

/* =========================
   BUILDING FILTER
========================= */
$buildingId = isset($_GET['building_id']) && is_numeric($_GET['building_id'])
    ? (int)$_GET['building_id']
    : null;

/* =========================
   STATUS FILTER
========================= */
$statusFilter = $_GET['status'] ?? '';

/* =========================
   PAGINATION
========================= */
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* =========================
   WHERE CLAUSE
========================= */
$where = [];

if ($buildingId !== null) {
    $where[] = "r.building_id = $buildingId";
}

if ($statusFilter !== '') {
    // UI -> DB enum
    $dbStatus = $statusUiToDb[$statusFilter] ?? $statusFilter;
    $where[] = "r.report_status = '".$conn->real_escape_string($dbStatus)."'";
}


$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* =========================
   STATUS CARD COUNTS
========================= */
$statSql = "
    SELECT report_status, COUNT(*) AS total
    FROM report
    ".($buildingId ? "WHERE building_id = $buildingId" : "")."
    GROUP BY report_status
";

$stats = [
    'New' => 0,
    'Pending' => 0,
    'Resolved' => 0
];

$statRes = $conn->query($statSql);

while ($row = $statRes->fetch_assoc()) {
    // DB -> UI enum
    $uiStatus = $statusDbToUi[$row['report_status']] ?? $row['report_status'];
    $stats[$uiStatus] = $row['total'];
}


/* =========================
   COUNT FOR PAGINATION
========================= */
$countSql = "
    SELECT COUNT(*) AS total
    FROM report r
    $whereSQL
";
$countRes = $conn->query($countSql);
$totalReports = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalReports / $limit);

/* =========================
   FETCH DATA
========================= */
$sql = "
SELECT 
    r.report_id,
    r.student_id,
    r.report_title,
    r.report_location,
    r.report_status,
    r.created_at
FROM report r
$whereSQL
ORDER BY r.created_at DESC
LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

$start = $totalReports > 0 ? $offset + 1 : 0;
$end   = min($offset + $limit, $totalReports);
?>

<main>
<style>
html, body {
    background:#f6f8fc !important;
}

/* =========================
   WRAPPER
========================= */
.report-wrapper {
    background:#fff;
    border-radius:24px;
    padding:3.5rem 3rem 10rem;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
}

/* =========================
   BANNER
========================= */
.report-banner {
    margin-top:-6rem;
    margin-bottom:-12rem;
    display:flex;
    justify-content:center;
}
.report-banner img {
    max-width:650px;
    width:100%;
}

/* =========================
   STATUS CARD GRID
========================= */
.student-section {
    margin-top:6rem;
}

.student-card-grid {
    display:grid;
    grid-template-columns:repeat(3,220px);
    gap:2rem;
    justify-content:center;
}

/* =========================
   BASE CARD
========================= */
.student-card {
    height:170px;
    border-radius:16px;
    padding:1.8rem 1.5rem;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
    text-decoration:none;
    box-shadow:0 10px 26px rgba(0,0,0,.08);
    transition:all .25s ease;
    overflow:hidden;
}

.student-card:hover {
    transform:translateY(-6px);
    box-shadow:0 18px 40px rgba(0,0,0,.12);
}

/* =========================
   GRADIENTS (LIKE DASHBOARD)
========================= */
.card-new {
    background:linear-gradient(135deg,#eaf2ff,#dbe9ff);
    color:#2456c3;
}

.card-pending {
    background:linear-gradient(135deg,#ffecec,#ffd6d6);
    color:#b42323;
}

.card-resolved {
    background:linear-gradient(135deg,#e9fff3,#d6f5e6);
    color:#1f9254;
}

/* =========================
   TEXT
========================= */
.card-text {
    font-size:1.05rem;
    font-weight:800;
    letter-spacing:.5px;
}

.card-text small {
    font-size:.85rem;
    opacity:.85;
}

/* =========================
   ICON
========================= */
.student-card img {
    width:90px;
    position:absolute;
    bottom:10px;
    right:10px;
    opacity:.85;
}

/* =========================
   SEARCH
========================= */
.report-search-wrapper {
    width:420px;
    position:relative;
}

.report-search-input {
    border-radius:50px;
    padding-right:3.2rem;
    height:48px;
}

.report-search-btn {
    position:absolute;
    right:6px;
    top:50%;
    transform:translateY(-50%);
    width:40px;
    height:40px;
    border-radius:50%;
    border:none;
    background:#5f6dff;
    color:#fff;
}

/* =========================
   FILTER
========================= */
.report-filters {
    display:flex;
    justify-content:center;
    gap:1.5rem;
    margin:12px 0 1.25rem;
}
</style>

<div class="container-fluid px-4">
<div class="report-wrapper">

    <!-- BANNER -->
    <div class="report-banner">
        <img src="../report.png" alt="Report Management">
    </div>

    <!-- STATUS CARDS -->
    <div class="student-section">
        <div class="student-card-grid">

            <a href="?building_id=<?= $buildingId ?>&status=New"
               class="student-card card-new">
                <span class="card-text">
                    NEW<br>
                    <small><?= $stats['New'] ?> Reports</small>
                </span>
                <img src="../new.png">
            </a>

            <a href="?building_id=<?= $buildingId ?>&status=Pending"
               class="student-card card-pending">
                <span class="card-text">
                    PENDING<br>
                    <small><?= $stats['Pending'] ?> Reports</small>
                </span>
                <img src="../pending.png">
            </a>

            <a href="?building_id=<?= $buildingId ?>&status=Resolved"
               class="student-card card-resolved">
                <span class="card-text">
                    RESOLVED<br>
                    <small><?= $stats['Resolved'] ?> Reports</small>
                </span>
                <img src="../resolved.png">
            </a>

        </div>
    </div>

    <!-- SEARCH + FILTER -->
    <div class="d-flex justify-content-center align-items-center gap-2 mt-5 mb-2">
        <div class="report-search-wrapper">
            <input id="reportSearch"
                   class="form-control report-search-input"
                   placeholder="Search"
                   onkeyup="applySearch()">
            <button class="report-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <select class="form-select rounded-pill px-3"
                style="width:170px"
                onchange="location='?building_id=<?= $buildingId ?>&status='+this.value">
            <option value="">All Status</option>
            <option value="New" <?= $statusFilter=='New'?'selected':'' ?>>New</option>
            <option value="Pending" <?= $statusFilter=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Resolved" <?= $statusFilter=='Resolved'?'selected':'' ?>>Resolved</option>
        </select>
    </div>

    <!-- SEARCH TYPE -->
    <div class="report-filters">
        <label><input type="radio" name="searchType" value="title" checked> Title</label>
        <label><input type="radio" name="searchType" value="student"> Student ID</label>
        <label><input type="radio" name="searchType" value="location"> Location</label>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-muted mb-2">
                Showing <?= $start ?> to <?= $end ?> of <b><?= $totalReports ?></b> reports
            </div>

            <table id="reportTable" class="table table-bordered align-middle text-center">
                <thead>
                <tr>
                    <th>Bil.</th>
                    <th class="text-start">Title</th>
                    <th>Student ID</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>View</th>
                </tr>
                </thead>
                <tbody>
              <?php
if ($totalReports == 0) {
    echo "<tr><td colspan='7'>No reports found</td></tr>";
} else {
    $i = $start;
    while ($row = $result->fetch_assoc()) {

        $uiStatus = $statusDbToUi[$row['report_status']] ?? $row['report_status'];

        echo "
        <tr>
            <td>$i</td>
            <td class='text-start'>".htmlspecialchars($row['report_title'])."</td>
            <td>".htmlspecialchars($row['student_id'])."</td>
            <td>".htmlspecialchars($row['report_location'])."</td>
            <td>".htmlspecialchars($uiStatus)."</td>
            <td>".htmlspecialchars($row['created_at'])."</td>
            <td>
                <a href='view_report.php?id={$row['report_id']}'
                   class='btn btn-info btn-sm rounded-pill px-4'>
                   View
                </a>
            </td>
        </tr>";
        $i++;
    }
}
?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
function applySearch() {
    const q = document.getElementById('reportSearch').value.toLowerCase();
    const type = document.querySelector('input[name="searchType"]:checked').value;
    const rows = document.querySelectorAll('#reportTable tbody tr');

    rows.forEach(row => {
        let text = '';
        if (type === 'title') text = row.cells[1].innerText.toLowerCase();
        else if (type === 'student') text = row.cells[2].innerText.toLowerCase();
        else text = row.cells[3].innerText.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
</main>

<?php include("../page/footer.php"); ?>
