<?php
include("../page/header.php");
include("../config/config.php");

if (!$conn) {
    die("<div class='alert alert-danger'>Database connection failed</div>");
}

/* =========================
   PAGINATION
========================= */
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* =========================
   FILTER (SERVER SIDE)
========================= */
$statusFilter = $_GET['status'] ?? '';

$where = [];
if ($statusFilter !== '') {
    $where[] = "report_status = '".$conn->real_escape_string($statusFilter)."'";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* =========================
   COUNT FOR PAGINATION
========================= */
$countRes = $conn->query("SELECT COUNT(*) AS total FROM report $whereSQL");
$totalReports = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalReports / $limit);

/* =========================
   FETCH DATA
========================= */
$sql = "SELECT report_id, student_id, report_title, report_location, report_status, created_at
        FROM report
        $whereSQL
        ORDER BY created_at DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$start = $totalReports > 0 ? $offset + 1 : 0;
$end   = min($offset + $limit, $totalReports);
?>

<main>
<style>
html, body { background: #ffffff !important; margin:0; padding:0; }
.table th, .table td { vertical-align: middle; background-color: #fff !important; }
.report-banner { margin-top: -6rem; margin-bottom: -7rem; display:flex; justify-content:center; }
.report-banner img { max-width:650px; width:100%; }
.report-search-wrapper { width:420px; position:relative; }
.report-search-input { border-radius:50px; padding-right:3.2rem; height:48px; }
.report-search-btn { position:absolute; right:6px; top:50%; transform:translateY(-50%); width:40px; height:40px; border-radius:50%; border:none; background:#5f6dff; color:#fff; }
.report-filters { display:flex; justify-content:center; gap:1.5rem; margin:.75rem 0 1.25rem; }
.status-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:6px; }
#noResults { display:none; text-align:center; font-weight:bold; margin-top:1rem; }
.report-filters { margin-top:12px; justify-content:center; }
.report-filters label { cursor:pointer; }
</style>

<div class="container-fluid px-4">

    <!-- BANNER -->
    <div class="report-banner">
        <img src="../report-banner.png" alt="Report Management">
    </div>

    <!-- SEARCH + FILTERS -->
    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">

        <!-- SEARCH (CLIENT SIDE) -->
        <div class="report-search-wrapper">
            <input id="reportSearch" type="text"
                   class="form-control report-search-input"
                   placeholder="Search by title"
                   onkeyup="applySearch()">
            <button class="report-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- STATUS FILTER (SERVER SIDE) -->
        <select class="form-select rounded-pill px-3"
                style="width:160px"
                onchange="location='?status='+this.value">
            <option value="">All Status</option>
            <option value="pending"  <?= $statusFilter=='pending'?'selected':'' ?>>Pending</option>
            <option value="approved" <?= $statusFilter=='approved'?'selected':'' ?>>Approved</option>
            <option value="in_progress" <?= $statusFilter=='in_progress'?'selected':'' ?>>In Progress</option>
            <option value="resolved" <?= $statusFilter=='resolved'?'selected':'' ?>>Resolved</option>
        </select>
    </div>

    <!-- SEARCH TYPE -->
    <div class="report-filters">
        <label><input type="radio" name="searchType" value="title" checked> Title</label>
        <label><input type="radio" name="searchType" value="student_id"> Student ID</label>
        <label><input type="radio" name="searchType" value="location"> Location</label>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="text-muted mb-2">
                Showing <?php echo $start; ?> to <?php echo $end; ?> of
                <b><?php echo $totalReports; ?></b> reports
            </div>

            <table id="reportTable" class="table table-bordered text-center align-middle">
                <thead>
                <tr>
                    <th>Bil.</th>
                    <th class="text-start">Title</th>
                    <th>Student ID</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>View</th>
                </tr>
                </thead>
                <tbody>

                <?php
                if ($totalReports == 0) {
                    echo "<tr><td colspan='7'>No results found</td></tr>";
                } else {
                    $i = $start;
                    while ($row = $result->fetch_assoc()) {

                        // Display real status from database
                        $status = htmlspecialchars($row['report_status']);

                        echo "<tr>
                            <td>$i</td>
                            <td class='text-start'>".htmlspecialchars($row['report_title'])."</td>
                            <td>".htmlspecialchars($row['student_id'])."</td>
                            <td>".htmlspecialchars($row['report_location'])."</td>
                            <td>$status</td>
                            <td>".htmlspecialchars($row['created_at'])."</td>
                            <td>
                                <a href='view_report.php?id={$row['report_id']}'
                                   class='btn btn-info btn-sm rounded-pill px-4'>View</a>
                            </td>
                        </tr>";
                        $i++;
                    }
                }
                ?>

                </tbody>
            </table>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
            <nav class="d-flex justify-content-end mt-3">
                <ul class="pagination pagination-sm">
                    <li class="page-item <?= $page<=1?'disabled':'' ?>">
                        <a class="page-link"
                           href="?page=<?= max(1,$page-1) ?>&status=<?= $statusFilter ?>">Prev</a>
                    </li>

                    <?php for ($p=1; $p<=$totalPages; $p++): ?>
                        <li class="page-item <?= $p==$page?'active':'' ?>">
                            <a class="page-link"
                               href="?page=<?= $p ?>&status=<?= $statusFilter ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                        <a class="page-link"
                           href="?page=<?= min($totalPages,$page+1) ?>&status=<?= $statusFilter ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

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
        else if (type === 'student_id') text = row.cells[2].innerText.toLowerCase();
        else text = row.cells[3].innerText.toLowerCase(); // Location column
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>

</main>

<?php include("../page/footer.php"); ?>
