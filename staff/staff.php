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
$roleFilter   = $_GET['role'] ?? '';

$where = [];
if ($statusFilter !== '') {
    $where[] = "status = '".$conn->real_escape_string($statusFilter)."'";
}
if ($roleFilter !== '') {
    $where[] = "role = '".$conn->real_escape_string($roleFilter)."'";
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* =========================
   COUNT FOR PAGINATION
========================= */
$countRes = $conn->query("SELECT COUNT(*) AS total FROM staff $whereSQL");
$totalStaff = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalStaff / $limit);

/* =========================
   FETCH DATA
========================= */
$sql = "SELECT staff_id, full_name, phone_no, role, status
        FROM staff
        $whereSQL
        ORDER BY full_name ASC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$start = $totalStaff > 0 ? $offset + 1 : 0;
$end   = min($offset + $limit, $totalStaff);
?>

<main>
<style>
html, body { background: #ffffff !important; margin:0; padding:0; }
.table th, .table td { vertical-align: middle; background-color: #fff !important; }
.student-banner { margin-top:-6rem; margin-bottom:-7rem; display:flex; justify-content:center; }
.student-banner img { max-width:650px; width:100%; }
.student-search-wrapper { width:420px; position:relative; }
.student-search-input { border-radius:50px; padding-right:3.2rem; height:48px; }
.student-search-btn { position:absolute; right:6px; top:50%; transform:translateY(-50%); width:40px; height:40px; border-radius:50%; border:none; background:#5f6dff; color:#fff; }
.student-filters { display:flex; justify-content:center; gap:1.5rem; margin:.75rem 0 1.25rem; }
.status-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:6px; }
.status-active { background:#2ecc71; }
.status-leave { background:#f1c40f; }
.status-archived { background:#bdc3c7; }
#noResults { display:none; text-align:center; font-weight:bold; margin-top:1rem; }
.student-filters { margin-top:12px; justify-content:center; }
.student-filters label { cursor:pointer; }
</style>

<div class="container-fluid px-4">

    <!-- BANNER -->
    <div class="student-banner">
        <img src="../staff-banner.png" alt="Staff Directory">
    </div>

    <!-- SEARCH + FILTERS + ADD -->
    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">

        <!-- SEARCH (CLIENT SIDE) -->
        <div class="student-search-wrapper">
            <input id="staffSearch" type="text"
                   class="form-control student-search-input"
                   placeholder="Search by name">
            <button class="student-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- ROLE FILTER (SERVER SIDE) -->
        <select class="form-select rounded-pill px-3"
                style="width:160px"
                onchange="location='?role='+this.value+'&status=<?php echo $statusFilter; ?>'">
            <option value="">All Roles</option>
            <option value="admin"    <?= $roleFilter=='admin'?'selected':'' ?>>Admin</option>
            <option value="warden"   <?= $roleFilter=='warden'?'selected':'' ?>>Warden</option>
            <option value="security" <?= $roleFilter=='security'?'selected':'' ?>>Security</option>
            <option value="cleaner"  <?= $roleFilter=='cleaner'?'selected':'' ?>>Cleaner</option>
        </select>

        <!-- STATUS FILTER (SERVER SIDE) -->
        <select class="form-select rounded-pill px-3"
                style="width:160px"
                onchange="location='?status='+this.value+'&role=<?php echo $roleFilter; ?>'">
            <option value="">All Status</option>
            <option value="active"    <?= $statusFilter=='active'?'selected':'' ?>>Active</option>
            <option value="on_leave"  <?= $statusFilter=='on_leave'?'selected':'' ?>>On Leave</option>
            <option value="archived"  <?= $statusFilter=='archived'?'selected':'' ?>>Archived</option>
        </select>

        <!-- ADD -->
        <a href="add_staff.php" class="btn btn-success rounded-pill px-4 py-2">
            <i class="fas fa-plus me-1"></i> Add Staff
        </a>
    </div>

    <!-- SEARCH TYPE (RADIO) -->
    <div class="student-filters">
        <label><input type="radio" name="searchType" value="name" checked> Name</label>
        <label><input type="radio" name="searchType" value="id"> Staff ID</label>
        <label><input type="radio" name="searchType" value="phone"> Phone Number</label>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="text-muted mb-2">
                Showing <?php echo $start; ?> to <?php echo $end; ?> of
                <b><?php echo $totalStaff; ?></b> staff
            </div>

            <table id="staffTable" class="table table-bordered text-center align-middle">
                <thead>
                <tr>
                    <th>Bil.</th>
                    <th class="text-start">Name</th>
                    <th>Staff ID</th>
                    <th>Role</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Edit</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($totalStaff == 0) {
                    echo "<tr><td colspan='7'>No results found</td></tr>";
                } else {
                    $i = $start;
                    while ($row = $result->fetch_assoc()) {
                        if ($row['status'] == 'active') {
                            $status = "<span class='status-dot status-active'></span>Active";
                        } elseif ($row['status'] == 'on_leave') {
                            $status = "<span class='status-dot status-leave'></span>On Leave";
                        } else {
                            $status = "<span class='status-dot status-archived'></span>Archived";
                        }

                        echo "<tr>
                            <td>$i</td>
                            <td class='text-start'>{$row['full_name']}</td>
                            <td>{$row['staff_id']}</td>
                            <td>".ucfirst($row['role'])."</td>
                            <td>{$row['phone_no']}</td>
                            <td>$status</td>
                            <td>
                                <a href='staff_details.php?id={$row['staff_id']}'
                                   class='btn btn-primary btn-sm rounded-pill px-4'>
                                   Edit
                                </a>
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
                           href="?page=<?= max(1,$page-1) ?>&status=<?= $statusFilter ?>&role=<?= $roleFilter ?>">
                           Prev
                        </a>
                    </li>

                    <?php for ($p=1; $p<=$totalPages; $p++): ?>
                        <li class="page-item <?= $p==$page?'active':'' ?>">
                            <a class="page-link"
                               href="?page=<?= $p ?>&status=<?= $statusFilter ?>&role=<?= $roleFilter ?>">
                               <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
                        <a class="page-link"
                           href="?page=<?= min($totalPages,$page+1) ?>&status=<?= $statusFilter ?>&role=<?= $roleFilter ?>">
                           Next
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
// ================================
// CLIENT-SIDE SEARCH + SORT
// ================================
const searchInput = document.getElementById('staffSearch');
const radios = document.querySelectorAll('input[name="searchType"]');
const table = document.getElementById('staffTable');
const tbody = table.tBodies[0];

let currentType = 'name';

radios.forEach(radio => {
    radio.addEventListener('change', () => {
        currentType = radio.value;
        applySearchAndSort();
    });
});

searchInput.addEventListener('keyup', () => {
    applySearchAndSort();
});

function applySearchAndSort() {
    const q = searchInput.value.toLowerCase();
    const rows = Array.from(tbody.rows);

    // 1️⃣ FILTER
    const filteredRows = rows.filter(row => {
        let text = '';
        if (currentType === 'name') text = row.cells[1].innerText.toLowerCase();
        else if (currentType === 'id') text = row.cells[2].innerText.toLowerCase();
        else text = row.cells[4].innerText.toLowerCase();
        return text.includes(q);
    });

    // 2️⃣ SORT
    let colIndex;
    if (currentType === 'name') colIndex = 1;
    else if (currentType === 'id') colIndex = 2;
    else colIndex = 4;

    filteredRows.sort((a, b) => {
        const textA = a.cells[colIndex].innerText.toLowerCase();
        const textB = b.cells[colIndex].innerText.toLowerCase();

        // For numeric columns (ID / Phone), sort numerically
        if (currentType === 'id' || currentType === 'phone') {
            return parseInt(textA.replace(/\D/g,'')) - parseInt(textB.replace(/\D/g,''));
        }
        return textA.localeCompare(textB);
    });

    // 3️⃣ RENDER
    tbody.innerHTML = '';
    filteredRows.forEach(row => tbody.appendChild(row));
}

// Initial load
applySearchAndSort();
</script>

</main>

<?php include("../page/footer.php"); ?>
