<?php
include("../config/config.php");

$building_id = $_GET['id'] ?? 0;
if ($building_id == 0) {
    die("Invalid building.");
}

include("../page/header.php");

/* ===== GET BUILDING NAME ===== */
$buildingSql = "SELECT building_name FROM building WHERE building_id = ?";
$stmt = $conn->prepare($buildingSql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$building = $stmt->get_result()->fetch_assoc();
$building_name = $building['building_name'] ?? 'Building';
?>

<main>

<style>
/* ===== TABLE ===== */
.table th, .table td {
    vertical-align: middle;
    background-color: #ffffff !important;
}
.table thead {
    background-color: #ffffff !important;
}

/* ===== MINI DASHBOARD (SATRIA STYLE) ===== */
.mini-dashboard {
    display: flex;
    justify-content: center;
    gap: 2.5rem;
    margin: 2rem 0 2.5rem;
}

.mini-card {
    width: 230px;
    height: 130px;
    border-radius: 14px;
    padding: 1.2rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    box-shadow: 0 12px 30px rgba(0,0,0,.12);
}

.mini-icon {
    width: 58px;
    height: 58px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mini-icon img {
    width: 100px;
    height: 100px;
    object-fit: contain;
}

.mini-text h6 {
    font-size: .75rem;
    font-weight: 600;
    margin-bottom: .2rem;
    text-transform: uppercase;
    color: #3b4a5a;
}

.mini-text .value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #111;
}

/* ===== SEARCH ===== */
.student-search-wrapper {
    width: 70%;
    position: relative;
}
.student-search-input {
    border-radius: 50px;
    padding-right: 3.2rem;
    height: 48px;
}
.student-search-btn {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    border: none;
    background-color: #5f6dff;
    color: #fff;
}

#noResults {
    display: none;
    font-weight: 700;
    text-align: center;
    margin-top: .5rem;
}
</style>

<div class="container-fluid px-4">

    <!-- ===== TITLE ===== -->
    <h2 class="text-center fw-bold mt-3">
        <?= htmlspecialchars($building_name); ?>
    </h2>

    <!-- ===== MINI DASHBOARD ===== -->
    <div class="mini-dashboard">

        <div class="mini-card" style="background:#eafaf1;">
            <div class="mini-icon" style="background:#00bf63;">
                <img src="../total-student.png" alt="Total Student">
            </div>
            <div class="mini-text">
                <h6>Total Student</h6>
                <div class="value">420</div>
            </div>
        </div>

        <div class="mini-card" style="background:#fff2f2;">
            <div class="mini-icon" style="background:#ff5757;">

                <img src="../total-capacity-used.png" alt="Capacity Used">
            </div>
            <div class="mini-text">
                <h6>Total Capacity Used</h6>
                <div class="value">70%</div>
            </div>
        </div>

        <div class="mini-card" style="background:#eef2ff;">
            <div class="mini-icon" style="background:#3f52e5;">
                <img src="../available-bed.png" alt="Available Bed">
            </div>
            <div class="mini-text">
                <h6>Available Bed</h6>
                <div class="value">35</div>
            </div>
        </div>

    </div>

    <!-- ===== SEARCH + ADD BLOCK ===== -->
    <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
        <div class="student-search-wrapper">
            <input id="blockSearch" type="text"
                   class="form-control student-search-input"
                   placeholder="Search block name">
            <button class="student-search-btn" onclick="applySearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <a href="add_block.php?building_id=<?= $building_id ?>"
           class="btn btn-success rounded-pill px-4 py-2">
            <i class="fas fa-plus me-1"></i> Add Block
        </a>
    </div>

    <!-- ===== BLOCK TABLE ===== -->
    <div class="card shadow-sm mt-2">
        <div class="card-body">

<?php
$blockSql = "
    SELECT block_id, block_name, total_level, student_gender
    FROM block
    WHERE building_id = ?
    ORDER BY block_name ASC
";
$stmt = $conn->prepare($blockSql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="alert alert-info text-center">No blocks found.</div>';
} else {
    echo '<div class="table-responsive">';
    echo '<table id="blockTable" class="table table-bordered text-center align-middle">';
    echo '<thead>
            <tr>
                <th>No.</th>
                <th class="text-start">Block Name</th>
                <th>Total Level</th>
                <th>Gender</th>
                <th>Manage</th>
            </tr>
          </thead><tbody>';

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$no}</td>
            <td class='text-start'>{$row['block_name']}</td>
            <td>{$row['total_level']}</td>
            <td>{$row['student_gender']}</td>
            <td>
                <a href='manage_block.php?id={$row['block_id']}'
                   class='btn btn-outline-primary btn-sm rounded-pill px-3'>
                   Manage
                </a>
            </td>
        </tr>";
        $no++;
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
    const q = document.getElementById('blockSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#blockTable tbody tr');
    let found = false;

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        if (name.includes(q)) {
            row.style.display = '';
            found = true;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('noResults').style.display = found ? 'none' : 'block';
}
</script>

</main>

<?php include("../page/footer.php"); ?>
