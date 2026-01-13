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
.room-banner h1 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.room-search-wrapper { width: 70%; position: relative; }
.room-search-input { border-radius: 50px; padding-right: 3.2rem; height: 48px; }

.room-search-btn {
    position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
    border-radius: 50%; width: 40px; height: 40px; border: none;
    background-color: #5f6dff; color: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
}

.room-filters { 
    display:flex; 
    justify-content:center; 
    gap:1.5rem; 
    margin-top:.75rem; 
    margin-bottom:1.25rem; 
}

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

.action-btns {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}
</style>

<div class="container-fluid px-4">

    <!-- Banner -->
    <div class="room-banner">
        <h1><i class="fas fa-door-open me-3"></i>Room Management</h1>
    </div>

    <!-- Search bar -->
    <div class="d-flex justify-content-center mb-1">
        <div class="room-search-wrapper">
            <input id="roomSearch" type="text" class="form-control room-search-input" placeholder="Search here">
            <button type="button" class="room-search-btn" onclick="applySearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <a href="add_room.php" class="btn btn-success rounded-pill px-4 py-2 ms-3">
            <i class="fas fa-plus me-1"></i> Add Room
        </a>
    </div>

    <!-- Filters -->
    <div class="room-filters">
        <label><input class="form-check-input" type="radio" name="searchType" value="room_no" checked> Room No</label>
        <label><input class="form-check-input" type="radio" name="searchType" value="wing"> Wing</label>
        <label><input class="form-check-input" type="radio" name="searchType" value="no_house"> House No</label>
        <label><input class="form-check-input" type="radio" name="searchType" value="status_bed"> Status</label>
    </div>

    <!-- Table -->
    <div class="card shadow-sm mt-2">
        <div class="card-body">

<?php
$sql = "
SELECT 
    room_id,
    wing,
    level,
    no_house,
    room_no,
    total_bed,
    bed_no,
    status_bed
FROM room
ORDER BY room_no ASC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    echo '<div class="table-responsive" style="max-height:520px; overflow:auto;">';
    echo '<table id="roomTable" class="table table-bordered text-center align-middle">';
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

    while ($row = $result->fetch_assoc()) {

        $room_id   = (int)$row['room_id'];
        $wing      = htmlspecialchars($row['wing']);
        $level     = htmlspecialchars($row['level']);
        $no_house  = htmlspecialchars($row['no_house']);
        $room_no   = htmlspecialchars($row['room_no']);
        $total_bed = htmlspecialchars($row['total_bed']);
        $bed_no    = htmlspecialchars($row['bed_no']);
        $status    = htmlspecialchars($row['status_bed']);

        $statusClass = 'status-available';
        if ($status === 'occupied') $statusClass = 'status-occupied';
        if ($status === 'maintenance') $statusClass = 'status-maintenance';

        echo "<tr>
                <td>$wing</td>
                <td>$level</td>
                <td>$no_house</td>
                <td>$room_no</td>
                <td>$total_bed</td>
                <td>$bed_no</td>
                <td><span class='status-badge $statusClass'>$status</span></td>
                <td>
                    <div class='action-btns'>
                        <a href='edit_room.php?id=$room_id' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                        <a href='room_details.php?id=$room_id' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i></a>
                        <form method='post' action='delete_room.php' style='display:inline;' 
                              onsubmit='return confirm(\"Delete this room?\");'>
                            <input type='hidden' name='room_id' value='$room_id'>
                            <button type='submit' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></button>
                        </form>
                    </div>
                </td>
              </tr>";
    }

    echo '</tbody></table></div>';
    echo '<div id="noResults">No results found</div>';
} else {
    echo '<div class="alert alert-info">No rooms found.</div>';
}
?>

        </div>
    </div>
</div>

<script>
function applySearch() {
    const q = document.getElementById('roomSearch').value.trim().toLowerCase();
    const type = document.querySelector('input[name="searchType"]:checked').value;
    const rows = document.querySelectorAll("#roomTable tbody tr");
    let found = false;

    rows.forEach(r => {
        let text = '';
        if (type === 'wing') text = r.cells[0].textContent;
        if (type === 'no_house') text = r.cells[2].textContent;
        if (type === 'room_no') text = r.cells[3].textContent;
        if (type === 'status_bed') text = r.cells[6].textContent;

        if (text.toLowerCase().includes(q)) {
            r.style.display = '';
            found = true;
        } else {
            r.style.display = 'none';
        }
    });

    document.getElementById('noResults').style.display = found ? 'none' : 'block';
}
</script>

</main>

<?php include("../page/footer.php"); ?>
