<?php
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4"><div class="alert alert-danger">Database connection not available.</div></div>');
}

/* ======================
   GET BLOCK ID
====================== */
$block_id = $_GET['block_id'] ?? '';

/* ======================
   RESET ROOM STATUS BY BLOCK
====================== */
if (isset($_GET['action']) && $_GET['action'] === 'reset_all' && $block_id !== '') {
    $resetSql = "UPDATE room SET status_bed = 'available' WHERE block_id = ?";
    $stmtReset = $conn->prepare($resetSql);
    $stmtReset->bind_param("i", $block_id);
    $stmtReset->execute();

    header("Location: room.php?block_id=$block_id&reset=success");
    exit;
}

/* ======================
   PAGINATION CONFIG
====================== */
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

/* ======================
   TOTAL COUNT (BY BLOCK)
====================== */
if ($block_id !== '') {
    $countSql = "SELECT COUNT(*) AS total FROM room WHERE block_id = ?";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->bind_param("i", $block_id);
    $stmtCount->execute();
    $countResult = $stmtCount->get_result();
} else {
    $countResult = $conn->query("SELECT COUNT(*) AS total FROM room");
}

$totalRows = (int)$countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$start = ($totalRows > 0) ? $offset + 1 : 0;
$end = min($offset + $limit, $totalRows);
?>

<main>
<style>
html, body { background: #ffffff !important; margin: 0; padding: 0; }
.table th, .table td { vertical-align: middle; background-color: #ffffff !important; }
.table thead { background-color: #f8f9fa !important; }

.room-banner {
    margin-top: 2rem;
    margin-bottom: 2rem;
    text-align: center;
    padding: 2rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
}

.room-search-wrapper { width:60%; position:relative; }
.room-search-input { border-radius:50px; padding-right:3.2rem; height:48px; }

.room-search-btn {
    position:absolute;
    right:6px;
    top:50%;
    transform:translateY(-50%);
    border-radius:50%;
    width:40px;
    height:40px;
    border:none;
    background:#5f6dff;
    color:#fff;
}

.room-filters {
    display:flex;
    justify-content:center;
    gap:1.5rem;
    margin:1rem 0;
}

.status-badge { padding:.35rem .75rem; border-radius:50px; font-size:.85rem; }
.status-available { background:#d4edda; color:#155724; }
.status-occupied { background:#fff3cd; color:#856404; }
.status-maintenance { background:#f8d7da; color:#721c24; }

.action-btns { display:flex; gap:.25rem; justify-content:center; }
</style>

<div class="container-fluid px-4">

    <!-- Banner -->
    <div class="room-banner">
        <h1><i class="fas fa-door-open me-2"></i>Room Management</h1>
    </div>

    <!-- Success message -->
    <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
        <div class="alert alert-success">
            ✅ Room statuses have been reset to <strong>AVAILABLE</strong> for this block.
        </div>
    <?php endif; ?>

    <!-- Search and buttons -->
    <div class="d-flex justify-content-center mb-1">
        <div class="room-search-wrapper">
            <input id="roomSearch" class="form-control room-search-input" placeholder="Search here">
            <button class="room-search-btn" onclick="applySearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <a href="add_room.php" class="btn btn-success rounded-pill px-4 py-2 ms-3">
            <i class="fas fa-plus me-1"></i> Add Room
        </a>

        <button class="btn btn-danger rounded-pill px-4 py-2 ms-2" onclick="confirmReset()">
            <i class="fas fa-rotate-left me-1"></i> Reset Status
        </button>
    </div>

    <!-- Filters -->
    <div class="room-filters">
        <label><input type="radio" name="searchType" value="room_no" checked> Room No</label>
        <label><input type="radio" name="searchType" value="wing"> Wing</label>
        <label><input type="radio" name="searchType" value="no_house"> House No</label>
        <label><input type="radio" name="searchType" value="status_bed"> Status</label>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="mb-2 text-muted">
                Showing <?= $start ?> to <?= $end ?> of <?= $totalRows ?> rooms
            </div>

<?php
/* ======================
   MAIN QUERY (BY BLOCK)
====================== */
if ($block_id !== '') {
    $sql = "
    SELECT room_id, block_id, wing, level, no_house, room_no, total_bed, bed_no, status_bed
    FROM room
    WHERE block_id = ?
    ORDER BY room_no ASC
    LIMIT $limit OFFSET $offset
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $block_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "
    SELECT room_id, block_id, wing, level, no_house, room_no, total_bed, bed_no, status_bed
    FROM room
    ORDER BY room_no ASC
    LIMIT $limit OFFSET $offset
    ";
    $result = $conn->query($sql);
}

if ($result && $result->num_rows > 0) {

    echo '<div class="table-responsive">';
    echo '<table id="roomTable" class="table table-bordered text-center">';
    echo '<thead>
        <tr>
            <th>Wing</th>
            <th>Level</th>
            <th>House No</th>
            <th>Room No</th>
            <th>Total Bed</th>
            <th>Bed No</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead><tbody>';

    while ($r = $result->fetch_assoc()) {

        $statusClass = 'status-available';
        if ($r['status_bed'] === 'occupied') $statusClass = 'status-occupied';
        if ($r['status_bed'] === 'maintenance') $statusClass = 'status-maintenance';

        echo "<tr>
            <td>{$r['wing']}</td>
            <td>{$r['level']}</td>
            <td>{$r['no_house']}</td>
            <td>{$r['room_no']}</td>
            <td>{$r['total_bed']}</td>
            <td>{$r['bed_no']}</td>
            <td><span class='status-badge $statusClass'>{$r['status_bed']}</span></td>
            <td>
                <div class='action-btns'>
                    <a href='edit_room.php?id={$r['room_id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                    <a href='room_details.php?id={$r['room_id']}' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
                    <form method='post' action='delete_room.php' onsubmit='return confirm(\"Delete this room?\");'>
                        <input type='hidden' name='room_id' value='{$r['room_id']}'>
                        <button class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></button>
                    </form>
                </div>
            </td>
        </tr>";
    }

    echo '</tbody></table></div>';
}
?>

<?php
/* ======================
   SMART PAGINATION
====================== */
$range = 2;
$startPage = max(1, $page - $range);
$endPage = min($totalPages, $page + $range);
?>

<nav class="d-flex justify-content-end mt-3">
<ul class="pagination pagination-sm">

<li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
<a class="page-link" href="?block_id=<?= $block_id ?>&page=<?= $page-1 ?>">Prev</a>
</li>

<?php for ($i=$startPage; $i<=$endPage; $i++): ?>
<li class="page-item <?= ($i==$page)?'active':'' ?>">
<a class="page-link" href="?block_id=<?= $block_id ?>&page=<?= $i ?>"><?= $i ?></a>
</li>
<?php endfor; ?>

<li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
<a class="page-link" href="?block_id=<?= $block_id ?>&page=<?= $page+1 ?>">Next</a>
</li>

</ul>
</nav>

        </div>
    </div>
</div>

<script>
function applySearch() {
    const q = document.getElementById('roomSearch').value.toLowerCase();
    const type = document.querySelector('input[name="searchType"]:checked').value;
    const rows = document.querySelectorAll("#roomTable tbody tr");

    rows.forEach(r => {
        let text='';
        if(type==='wing') text=r.cells[0].innerText;
        if(type==='no_house') text=r.cells[2].innerText;
        if(type==='room_no') text=r.cells[3].innerText;
        if(type==='status_bed') text=r.cells[6].innerText;
        r.style.display = text.toLowerCase().includes(q) ? '' : 'none';
    });
}

// Reset button with double confirmation
function confirmReset() {
    if (!confirm("Reset ALL room status to AVAILABLE for this block?")) return;
    if (!confirm("This action cannot be undone.\nClick OK to confirm.")) return;

    const blockId = "<?= $block_id ?>";
    window.location.href = "?action=reset_all&block_id=" + blockId;
}
</script>

</main>

<?php include("../page/footer.php"); ?>
