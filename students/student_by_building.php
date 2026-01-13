<?php
include("../page/header.php");
include("../config/config.php");

/* =========================
   DB CHECK
========================= */
if (!isset($conn) || !$conn) {
    die('<div class="container mt-4"><div class="alert alert-danger">
        Database connection not available.
    </div></div>');
}

/* =========================
   GET BUILDING ID
========================= */
$building_id = isset($_GET['building_id']) ? (int)$_GET['building_id'] : 0;

if ($building_id <= 0) {
    die('<div class="container mt-4"><div class="alert alert-warning">
        Invalid building selected.
    </div></div>');
}

/* =========================
   FETCH BUILDING NAME
========================= */
$stmtB = $conn->prepare("
    SELECT building_name
    FROM building
    WHERE building_id = ?
    LIMIT 1
");
$stmtB->bind_param("i", $building_id);
$stmtB->execute();
$resB = $stmtB->get_result();

if ($resB->num_rows === 0) {
    die('<div class="container mt-4"><div class="alert alert-warning">
        Building not found.
    </div></div>');
}

$pageTitle = strtoupper($resB->fetch_assoc()['building_name']);
?>

<main>
<style>
html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}
.table th, .table td { 
    vertical-align: middle; 
    background-color: #ffffff !important;
}
.table thead { background-color: #ffffff !important; }

.student-banner {
    margin-top: -6rem;
    margin-bottom: -7rem;
    text-align: center;
    display: flex;
    justify-content: center;
}
.student-banner img {
    max-width: 650px;
    width: 100%;
    height: auto;
}

.student-back { margin-top:.5rem; margin-bottom:.5rem; }
.student-back a { text-decoration:none; color:#000; font-size:.95rem; }

.student-search-wrapper { width:70%; }
.student-search-input { border-radius:50px; padding-right:3.2rem; height:48px; }

.student-search-btn {
    position:absolute; right:6px; top:50%;
    transform:translateY(-50%);
    border-radius:50%;
    width:40px; height:40px;
    border:none;
    background:#5f6dff;
    color:#fff;
    display:flex; align-items:center; justify-content:center;
}

.student-filters {
    display:flex;
    justify-content:center;
    gap:1.5rem;
    margin-top:.75rem;
    margin-bottom:1.25rem;
}

.card.table-card { margin-top:.75rem; }

#noResults {
    display:none;
    padding:1rem;
    font-weight:700;
    text-align:center;
    margin-top:.5rem;
    color:#333;
}
</style>

<div class="container-fluid px-4">

    <!-- Banner -->
    <div class="student-banner">
        <img src="../student-banner.png" alt="Student Directory">
    </div>

    <!-- Back -->
    <div class="student-back">
        <a href="student_section.php">← Back to Student List</a>
    </div>

    <!-- Title -->
    <h3 class="text-center fw-bold mb-3">
        Students in <?= htmlspecialchars($pageTitle) ?>
    </h3>

    <!-- Search + Download (ONLY CHANGE HERE) -->
    <div class="d-flex justify-content-center mb-1">
        <div class="student-search-wrapper d-flex align-items-center gap-2">

            <div class="position-relative flex-grow-1">
                <input id="studentSearch" type="text"
                       class="form-control student-search-input"
                       placeholder="Search here">
                <button type="button" class="student-search-btn" onclick="applySearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <a href="download_student_choice.php?building_id=<?= $building_id ?>"
               class="btn btn-outline-primary rounded-circle"
               style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-download"></i>
            </a>

        </div>
    </div>

    <!-- Filter -->
    <div class="student-filters">
        <label><input type="radio" name="searchType" value="name" checked> Name</label>
        <label><input type="radio" name="searchType" value="matric"> Matric Number</label>
        <label><input type="radio" name="searchType" value="phone"> Phone Number</label>
    </div>

    <!-- Table -->
    <div class="card shadow-sm mt-2 table-card">
        <div class="card-body">

<?php
$sql = "
SELECT DISTINCT
    s.student_id,
    s.full_name,
    s.phone_no
FROM student s
JOIN booking bkg   ON s.student_id = bkg.student_id
JOIN room r        ON bkg.room_id = r.room_id
JOIN block blk     ON r.block_id = blk.block_id
JOIN building bld  ON blk.building_id = bld.building_id
WHERE bld.building_id = ?
  AND s.status = 'active'
ORDER BY s.full_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="alert alert-info text-center">
        No students found in this building.
    </div>';
} else {

    echo '<div class="table-responsive" style="max-height:520px; overflow:auto;">';
    echo '<table id="studentsTable" class="table table-bordered text-center align-middle">';
    echo '<thead><tr>
            <th>Bil.</th>
            <th class="text-start">Name</th>
            <th>Matric Number</th>
            <th>Phone Number</th>
            <th>Details</th>
          </tr></thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>$i</td>
            <td class='text-start'>".htmlspecialchars($row['full_name'])."</td>
            <td>{$row['student_id']}</td>
            <td>{$row['phone_no']}</td>
            <td>
                <a href='student_details.php?id={$row['student_id']}'
                   class='btn btn-primary btn-sm rounded-pill px-3'>
                   More Details
                </a>
            </td>
        </tr>";
        $i++;
    }

    echo '</tbody></table></div>';
    echo '<div id="noResults">No results found</div>';
}
?>

        </div>
    </div>

</div>

<script>
function applySearch() {
    const q = document.getElementById('studentSearch').value.toLowerCase();
    const type = document.querySelector('input[name="searchType"]:checked').value;
    const rows = document.querySelectorAll("#studentsTable tbody tr");
    let found = false;

    rows.forEach(r => {
        let text = type === 'name' ? r.cells[1].innerText :
                   type === 'matric' ? r.cells[2].innerText :
                   r.cells[3].innerText;

        if (text.toLowerCase().includes(q)) {
            r.style.display = '';
            found = true;
        } else {
            r.style.display = 'none';
        }
    });

    document.getElementById("noResults").style.display = found ? "none" : "block";
}
</script>

</main>

<?php include("../page/footer.php"); ?>
