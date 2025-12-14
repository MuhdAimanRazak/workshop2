<?php 
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4"><div class="alert alert-danger">Database connection not available.</div></div>');
}
?>

<main>
    <style>
        /* ===== Staff Page Custom Styles (UNCHANGED UI) ===== */
        .table th, .table td { 
            vertical-align: middle; 
            background-color: #ffffff !important;
        }
        .table thead { background-color: #ffffff !important; }

        .student-banner {
            margin-top: -10rem;
            margin-bottom: -26rem;
            text-align: center;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .student-banner img {
            max-width: 1600px;
            width: 130%;
            margin-left: -250px;
            height: auto;
        }

        .student-back { margin-top: .5rem; margin-bottom: .5rem; }
        .student-back a { text-decoration: none; color: #000; font-size: 0.95rem; }

        .student-search-wrapper { width: 70%; position: relative; }
        .student-search-input { border-radius: 50px; padding-right: 3.2rem; height: 48px; }

        .student-search-btn {
            position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
            border-radius: 50%; width: 40px; height: 40px; border: none;
            background-color: #5f6dff; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        .student-filters { display:flex; justify-content:center; gap:1.5rem; margin-top:.75rem; margin-bottom:1.25rem; }
        .card.table-card { margin-top: .75rem; }

        #noResults {
            display:none;
            padding: 1rem;
            font-weight:700;
            text-align:center;
            margin-top: 0.5rem;
            color:#333;
        }
    </style>

    <div class="container-fluid px-4">

        <!-- Banner -->
        <div class="student-banner">
            <img src="../staffsearch.png" alt="Staff Directory">
        </div>


        <!-- Search bar -->
        <div class="d-flex justify-content-center mb-1">
            <div class="student-search-wrapper">
                <input id="staffSearch" type="text" class="form-control student-search-input" 
                       placeholder="Search here">
                <button type="button" class="student-search-btn" onclick="applySearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Add Staff Button -->
            <a href="add_staff.php" class="btn btn-success rounded-pill px-4 py-2 ms-3">
                <i class="fas fa-plus me-1"></i> Add Staff
            </a>
        </div>

        <!-- Radio filter -->
        <div class="student-filters">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="name" checked> Name
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="id"> Staff ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="phone"> Phone Number
            </label>
        </div>

        <!-- Staff Table -->
        <div class="card shadow-sm mt-2 table-card">
            <div class="card-body">

<?php
$sql = "SELECT staff_id, full_name, phone_no FROM staff ORDER BY full_name ASC";
$result = $conn->query($sql);

if ($result === false) {
    echo '<div class="alert alert-danger">Database query error: ' . htmlspecialchars($conn->error) . '</div>';
} 
else if ($result->num_rows === 0) {
    echo '<div class="alert alert-info">No staff found in the database.</div>';
} 
else {
    echo '<div class="table-responsive" style="max-height:520px; overflow:auto;">';
    echo '<table id="staffTable" class="table table-bordered text-center align-middle">';
    echo '<thead><tr>
            <th>Bil.</th>
            <th class="text-start">Name</th>
            <th>Staff ID</th>
            <th>Phone Number</th>
            <th>Details</th>
          </tr></thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['staff_id']);
        $name = htmlspecialchars($row['full_name']);
        $phone = htmlspecialchars($row['phone_no']);
        $detailsLink = 'staff_details.php?id=' . $id;

        echo "<tr>
                <td>$i</td>
                <td class='text-start'>$name</td>
                <td>$id</td>
                <td>$phone</td>
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
            const q = document.getElementById('staffSearch').value.trim().toLowerCase();
            const type = document.querySelector('input[name="searchType"]:checked').value;
            const table = document.getElementById('staffTable');
            const noResults = document.getElementById('noResults');

            if (!table) return;
            const rows = table.tBodies[0].rows;
            let found = false;

            for (let r of rows) {
                let text;
                if (type === 'name') text = r.cells[1].textContent.toLowerCase();
                else if (type === 'id') text = r.cells[2].textContent.toLowerCase();
                else text = r.cells[3].textContent.toLowerCase();

                if (text.includes(q)) {
                    r.style.display = '';
                    found = true;
                } else {
                    r.style.display = 'none';
                }
            }

            noResults.style.display = found ? 'none' : 'block';
        }

        document.getElementById('staffSearch').addEventListener('keydown', function(e){
            if (e.key === 'Enter') applySearch();
        });
    </script>

</main>

<?php include("../page/footer.php"); ?>
