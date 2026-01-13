<?php 
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4"><div class="alert alert-danger">Database connection not available.</div></div>');
}
?>

<main>
    <style>
        /* ===== Facility Page Custom Styles ===== */
        .table th, .table td { 
            vertical-align: middle; 
            background-color: #ffffff !important;
        }
        .table thead { background-color: #ffffff !important; }

        .facility-banner {
            margin-top: -10rem;
            margin-bottom: -26rem;
            text-align: center;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .facility-banner img {
            max-width: 1600px;
            width: 130%;
            margin-left: -250px;
            height: auto;
        }

        .facility-back { margin-top: .5rem; margin-bottom: .5rem; }
        .facility-back a { text-decoration: none; color: #000; font-size: 0.95rem; }

        .facility-search-wrapper { width: 70%; position: relative; }
        .facility-search-input { border-radius: 50px; padding-right: 3.2rem; height: 48px; }

        .facility-search-btn {
            position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
            border-radius: 50%; width: 40px; height: 40px; border: none;
            background-color: #5f6dff; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        .facility-filters { display:flex; justify-content:center; gap:1.5rem; margin-top:.75rem; margin-bottom:1.25rem; }
        .card.table-card { margin-top: .75rem; }

        #noResults {
            display:none;
            padding: 1rem;
            font-weight:700;
            text-align:center;
            margin-top: 0.5rem;
            color:#333;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-available { background-color: #d4edda; color: #155724; }
        .status-occupied { background-color: #fff3cd; color: #856404; }
        .status-maintenance { background-color: #f8d7da; color: #721c24; }
    </style>

    <div class="container-fluid px-4">

        <!-- Banner -->
        <div class="facility-banner">
            <img src="../facilitysearch.png" alt="Facility Directory">
        </div>


        <!-- Search bar -->
        <div class="d-flex justify-content-center mb-1">
            <div class="facility-search-wrapper">
                <input id="facilitySearch" type="text" class="form-control facility-search-input" 
                       placeholder="Search here">
                <button type="button" class="facility-search-btn" onclick="applySearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Add Facility Button -->
            <a href="add_facility.php" class="btn btn-success rounded-pill px-4 py-2 ms-3">
                <i class="fas fa-plus me-1"></i> Add Facility
            </a>
        </div>

        <!-- Radio filter -->
        <div class="facility-filters">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="name" checked> Facility Name
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="facility_id"> Facility ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="block_id"> Block ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="status"> Status
            </label>
        </div>

        <!-- Facility Table -->
        <div class="card shadow-sm mt-2 table-card">
            <div class="card-body">

<?php
$sql = "SELECT facility_id, block_id, facility_name, status FROM facility ORDER BY facility_name ASC";
$result = $conn->query($sql);

if ($result === false) {
    echo '<div class="alert alert-danger">Database query error: ' . htmlspecialchars($conn->error) . '</div>';
} 
else if ($result->num_rows === 0) {
    echo '<div class="alert alert-info">No facilities found in the database.</div>';
} 
else {
    echo '<div class="table-responsive" style="max-height:520px; overflow:auto;">';
    echo '<table id="facilityTable" class="table table-bordered text-center align-middle">';
    echo '<thead><tr>
            <th>Bil.</th>
            <th class="text-start">Facility Name</th>
            <th>Facility ID</th>
            <th>Block ID</th>
            <th>Status</th>
            <th>Details</th>
          </tr></thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $facilityId = htmlspecialchars($row['facility_id']);
        $blockId = htmlspecialchars($row['block_id']);
        $facilityName = htmlspecialchars($row['facility_name']);
        $status = htmlspecialchars($row['status']);
        $detailsLink = 'facility_details.php?id=' . $facilityId;

        // Determine status badge class
        $statusClass = 'status-available';
        if (strtolower($status) === 'occupied') {
            $statusClass = 'status-occupied';
        } elseif (strtolower($status) === 'maintenance') {
            $statusClass = 'status-maintenance';
        }

        echo "<tr>
                <td>$i</td>
                <td class='text-start'>$facilityName</td>
                <td>$facilityId</td>
                <td>$blockId</td>
                <td><span class='status-badge $statusClass'>$status</span></td>
                <td>
                    <a href='$detailsLink' class='btn btn-primary btn-sm rounded-pill px-3'>
                        More Details
                    </a>
                </td>
              </tr>";
        $i++;
    }

    echo '</tbody></table></div>';
    echo '<div id="noResults">No results found</div>';

    $result->free();
}
?>

            </div>
        </div>

    </div>

    <script>
        function applySearch() {
            const q = document.getElementById('facilitySearch').value.trim().toLowerCase();
            const type = document.querySelector('input[name="searchType"]:checked').value;
            const table = document.getElementById('facilityTable');
            const noResults = document.getElementById('noResults');

            if (!table) return;
            const rows = table.tBodies[0].rows;
            let found = false;

            for (let r of rows) {
                let text;
                if (type === 'name') text = r.cells[1].textContent.toLowerCase();
                else if (type === 'facility_id') text = r.cells[2].textContent.toLowerCase();
                else if (type === 'block_id') text = r.cells[3].textContent.toLowerCase();
                else if (type === 'status') text = r.cells[4].textContent.toLowerCase();

                if (text.includes(q)) {
                    r.style.display = '';
                    found = true;
                } else {
                    r.style.display = 'none';
                }
            }

            noResults.style.display = found ? 'none' : 'block';
        }

        document.getElementById('facilitySearch').addEventListener('keydown', function(e){
            if (e.key === 'Enter') applySearch();
        });
    </script>

</main>

<?php include("../page/footer.php"); ?>