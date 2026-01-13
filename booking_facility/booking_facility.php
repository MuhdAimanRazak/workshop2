<?php 
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4"><div class="alert alert-danger">Database connection not available.</div></div>');
}
?>

<main>
    <style>
        /* ===== Booking Facility Page Custom Styles ===== */
        .table th, .table td { 
            vertical-align: middle; 
            background-color: #ffffff !important;
        }
        .table thead { background-color: #ffffff !important; }

        .booking-banner {
            margin-top: -10rem;
            margin-bottom: -26rem;
            text-align: center;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .booking-banner img {
            max-width: 1600px;
            width: 130%;
            margin-left: -250px;
            height: auto;
        }

        .booking-back { margin-top: .5rem; margin-bottom: .5rem; }
        .booking-back a { text-decoration: none; color: #000; font-size: 0.95rem; }

        .booking-search-wrapper { width: 70%; position: relative; }
        .booking-search-input { border-radius: 50px; padding-right: 3.2rem; height: 48px; }

        .booking-search-btn {
            position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
            border-radius: 50%; width: 40px; height: 40px; border: none;
            background-color: #5f6dff; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        .booking-filters { display:flex; justify-content:center; gap:1.5rem; margin-top:.75rem; margin-bottom:1.25rem; }
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
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-cancelled { background-color: #e2e3e5; color: #383d41; }
        .status-completed { background-color: #d1ecf1; color: #0c5460; }
    </style>

    <div class="container-fluid px-4">

        <!-- Banner -->
        <div class="booking-banner">
            <img src="../facilitysearch.png" alt="Booking Facility Directory">
        </div>


        <!-- Search bar -->
        <div class="d-flex justify-content-center mb-1">
            <div class="booking-search-wrapper">
                <input id="bookingSearch" type="text" class="form-control booking-search-input" 
                       placeholder="Search here">
                <button type="button" class="booking-search-btn" onclick="applySearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Add Booking Button -->
            <a href="addbooking_facility.php" class="btn btn-success rounded-pill px-4 py-2 ms-3">
                <i class="fas fa-plus me-1"></i> Add Booking
            </a>
        </div>

        <!-- Radio filter -->
        <div class="booking-filters">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="booking_id" checked> Booking ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="facility_id"> Facility ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="student_id"> Student ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="staff_id"> Staff ID
            </label>
            <label class="form-check-label">
                <input class="form-check-input" type="radio" name="searchType" value="status"> Status
            </label>
        </div>

        <!-- Booking Table -->
        <div class="card shadow-sm mt-2 table-card">
            <div class="card-body">

<?php
$sql = "SELECT 
            booking_facility_id, 
            facility_id, 
            student_id, 
            staff_id, 
            start_date, 
            end_date, 
            status 
        FROM booking_facility 
        ORDER BY start_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    echo '<div class="alert alert-danger">Database query error: ' . htmlspecialchars($conn->error) . '</div>';
} 
else if ($result->num_rows === 0) {
    echo '<div class="alert alert-info">No bookings found in the database.</div>';
} 
else {
    echo '<div class="table-responsive" style="max-height:520px; overflow:auto;">';
    echo '<table id="bookingTable" class="table table-bordered text-center align-middle">';
    echo '<thead><tr>
            <th>Bil.</th>
            <th>Booking ID</th>
            <th>Facility ID</th>
            <th>Student ID</th>
            <th>Staff ID</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Details</th>
          </tr></thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $bookingId = htmlspecialchars($row['booking_facility_id']);
        $facilityId = htmlspecialchars($row['facility_id']);
        $studentId = $row['student_id'] ? htmlspecialchars($row['student_id']) : '-';
        $staffId = $row['staff_id'] ? htmlspecialchars($row['staff_id']) : '-';
        $startDate = htmlspecialchars($row['start_date']);
        $endDate = htmlspecialchars($row['end_date']);
        $status = htmlspecialchars($row['status']);
        $detailsLink = 'booking_facility_details.php?id=' . $bookingId;

        // Format dates for display
        $startDisplay = date('d/m/Y H:i', strtotime($startDate));
        $endDisplay = date('d/m/Y H:i', strtotime($endDate));

        // Determine status badge class
        $statusClass = 'status-pending';
        $statusLower = strtolower($status);
        if ($statusLower === 'approved') {
            $statusClass = 'status-approved';
        } elseif ($statusLower === 'rejected') {
            $statusClass = 'status-rejected';
        } elseif ($statusLower === 'cancelled') {
            $statusClass = 'status-cancelled';
        } elseif ($statusLower === 'completed') {
            $statusClass = 'status-completed';
        }

        echo "<tr>
                <td>$i</td>
                <td>$bookingId</td>
                <td>$facilityId</td>
                <td>$studentId</td>
                <td>$staffId</td>
                <td>$startDisplay</td>
                <td>$endDisplay</td>
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
            const q = document.getElementById('bookingSearch').value.trim().toLowerCase();
            const type = document.querySelector('input[name="searchType"]:checked').value;
            const table = document.getElementById('bookingTable');
            const noResults = document.getElementById('noResults');

            if (!table) return;
            const rows = table.tBodies[0].rows;
            let found = false;

            for (let r of rows) {
                let text;
                if (type === 'booking_id') text = r.cells[1].textContent.toLowerCase();
                else if (type === 'facility_id') text = r.cells[2].textContent.toLowerCase();
                else if (type === 'student_id') text = r.cells[3].textContent.toLowerCase();
                else if (type === 'staff_id') text = r.cells[4].textContent.toLowerCase();
                else if (type === 'status') text = r.cells[7].textContent.toLowerCase();

                if (text.includes(q)) {
                    r.style.display = '';
                    found = true;
                } else {
                    r.style.display = 'none';
                }
            }

            noResults.style.display = found ? 'none' : 'block';
        }

        document.getElementById('bookingSearch').addEventListener('keydown', function(e){
            if (e.key === 'Enter') applySearch();
        });
    </script>

</main>

<?php include("../page/footer.php"); ?>