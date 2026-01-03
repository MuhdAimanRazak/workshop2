<?php
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4"><div class="alert alert-danger">Database connection not available.</div></div>');
}
?>

<main>
<style>
    .table th, .table td {
        vertical-align: middle;
        background-color: #ffffff !important;
    }

    .table thead {
        background-color: #ffffff !important;
    }

    /* ===============================
       BANNER
    ================================ */
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

    .card.table-card { margin-top: .75rem; }

    /* ===============================
       SEARCH BAR
    ================================ */
    .search-wrapper {
        width: 60%;
        position: relative;
    }

    .search-input {
        border-radius: 50px;
        height: 48px;
        padding-right: 3rem;
    }

    .search-btn {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background-color: #5f6dff;
        color: #fff;
    }

    /* ===============================
       FORCE CENTER (DO NOT TOUCH HTML)
    ================================ */
    .search-row {
        justify-content: center !important;
        gap: 1.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-wrapper {
            width: 100%;
        }

        .search-row {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="container-fluid px-4">

    <!-- Banner -->
    <div class="student-banner">
        <img src="../building-banner.png" alt="Building Directory">
    </div>

    <!-- SEARCH + ADD BUTTON -->
    <div class="d-flex align-items-center justify-content-between mb-3 search-row">

        <!-- Search -->
        <div class="search-wrapper">
            <input id="buildingSearch"
                   type="text"
                   class="form-control search-input"
                   placeholder="Search building name">
            <button type="button" class="search-btn" onclick="applySearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>


    </div>

    <!-- Table -->
    <div class="card shadow-sm table-card">
        <div class="card-body">

<?php
$sql = "SELECT building_id, building_name, status 
        FROM building 
        WHERE status = 'Inactive'
        ORDER BY building_name ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo '<div class="alert alert-danger">Query error</div>';
}
elseif ($result->num_rows === 0) {
    echo '<div class="alert alert-info">No buildings found.</div>';
}
else {
    echo '<div class="table-responsive" style="max-height:520px; overflow:auto;">';
    echo '<table id="buildingTable" class="table table-bordered text-center align-middle">';
    echo '<thead>
            <tr>
                <th>No.</th>
                <th class="text-start">Building Name</th>
                <th>Status</th>
                <th>Manage</th>
            </tr>
          </thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {

        $id = $row['building_id'];
        $name = htmlspecialchars($row['building_name']);
        $status = htmlspecialchars($row['status']);

        $badge = ($status === 'Active')
            ? "<span class='badge bg-success'>Active</span>"
            : "<span class='badge bg-secondary'>Inactive</span>";

        echo "<tr>
                <td>$i</td>
                <td class='text-start'>$name</td>
                <td>$badge</td>
                <td>
                    <a href='manage_blocks.php?id=$id' 
                       class='btn btn-outline-primary btn-sm rounded-pill px-3'>
                        Manage
                    </a>
                </td>
              </tr>";
        $i++;
    }

    echo '</tbody></table></div>';
    $result->free();
}
?>

        </div>
    </div>

</div>

<!-- SEARCH SCRIPT -->
<script>
function applySearch() {
    const q = document.getElementById('buildingSearch').value
                .trim().toLowerCase();
    const table = document.getElementById('buildingTable');
    if (!table) return;

    const rows = table.tBodies[0].rows;

    for (let r of rows) {
        const name = r.cells[1].textContent.toLowerCase();
        r.style.display = name.includes(q) ? '' : 'none';
    }
}

document.getElementById('buildingSearch').addEventListener('keydown', function(e){
    if (e.key === 'Enter') applySearch();
});
</script>

</main>

<?php include("../page/footer.php"); ?>
