<?php
include("../config/config.php");

/* =========================
   SAVE DATA (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =========================
    // FORM DATA
    // =========================
    $facility_id = trim($_POST['facility_id']);
    $student_id  = !empty($_POST['student_id']) ? trim($_POST['student_id']) : NULL;
    $staff_id    = !empty($_POST['staff_id']) ? trim($_POST['staff_id']) : NULL;
    $start_date  = trim($_POST['start_date']) . ' ' . trim($_POST['start_time']) . ':00';
    $end_date    = trim($_POST['end_date']) . ' ' . trim($_POST['end_time']) . ':00';
    $status      = $_POST['status'];
    $notes       = trim($_POST['notes']);

    // =========================
    // VALIDATE STUDENT_ID OR STAFF_ID EXISTS
    // =========================
    if ($student_id !== NULL) {
        $check_student = $conn->prepare("SELECT student_id FROM student WHERE student_id = ?");
        $check_student->bind_param("s", $student_id);
        $check_student->execute();
        $check_student->store_result();
        
        if ($check_student->num_rows === 0) {
            echo "<script>
                alert('Error: Student ID \"$student_id\" does not exist!');
                window.history.back();
            </script>";
            exit;
        }
        $check_student->close();
    }
    
    if ($staff_id !== NULL) {
        $check_staff = $conn->prepare("SELECT staff_id FROM staff WHERE staff_id = ?");
        $check_staff->bind_param("s", $staff_id);
        $check_staff->execute();
        $check_staff->store_result();
        
        if ($check_staff->num_rows === 0) {
            echo "<script>
                alert('Error: Staff ID \"$staff_id\" does not exist!');
                window.history.back();
            </script>";
            exit;
        }
        $check_staff->close();
    }

    // =========================
    // CHECK FACILITY STATUS
    // =========================
    $check_facility = $conn->prepare("SELECT facility_name, status FROM facility WHERE facility_id = ?");
    $check_facility->bind_param("s", $facility_id);
    $check_facility->execute();
    $facility_result = $check_facility->get_result();
    
    if ($facility_result->num_rows === 0) {
        echo "<script>
            alert('Error: Facility does not exist!');
            window.history.back();
        </script>";
        exit;
    }
    
    $facility_data = $facility_result->fetch_assoc();
    $check_facility->close();

    // =========================
    // CHECK FOR BOOKING CLASH (including status-based unavailability)
    // =========================
    $check_clash = $conn->prepare("
        SELECT booking_facility_id, start_date, end_date, status
        FROM booking_facility 
        WHERE facility_id = ? 
        AND status NOT IN ('Rejected', 'Cancelled')
        AND (
            (start_date < ? AND end_date > ?) OR
            (start_date < ? AND end_date > ?) OR
            (start_date >= ? AND end_date <= ?)
        )
    ");
    
    $check_clash->bind_param(
        "sssssss",
        $facility_id,
        $end_date, $start_date,
        $end_date, $end_date,
        $start_date, $end_date
    );
    
    $check_clash->execute();
    $clash_result = $check_clash->get_result();
    
    if ($clash_result->num_rows > 0) {
        $clash_booking = $clash_result->fetch_assoc();
        echo "<script>
            alert('Error: This facility is already booked during the selected time period (Status: " . addslashes($clash_booking['status']) . "). Please choose a different time or facility.');
            window.history.back();
        </script>";
        exit;
    }
    $check_clash->close();

    // =========================
    // INSERT DATA (booking_facility_id AUTO INCREMENT)
    // =========================
    $sql = "INSERT INTO booking_facility (
                facility_id,
                student_id,
                staff_id,
                start_date,
                end_date,
                status,
                notes
            ) VALUES (?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssss",
        $facility_id,
        $student_id,
        $staff_id,
        $start_date,
        $end_date,
        $status,
        $notes
    );

    $stmt->execute();

    // Get the auto-generated booking_facility_id
    $booking_facility_id = $conn->insert_id;

    echo "<script>
        alert('Booking successfully added!');
        window.location.href = 'booking_facility_details.php?id=$booking_facility_id';
    </script>";
    exit;
}

// =========================
// FETCH BUILDINGS FOR DROPDOWN
// =========================
$buildings_sql = "SELECT building_id, building_name FROM building ORDER BY building_name ASC";
$buildings_result = $conn->query($buildings_sql);

// =========================
// FETCH ALL BLOCKS WITH BUILDING INFO
// =========================
$blocks_sql = "SELECT block_id, block_name, building_id FROM block ORDER BY block_name ASC";
$blocks_result = $conn->query($blocks_sql);
$blocks_data = [];
if ($blocks_result && $blocks_result->num_rows > 0) {
    while ($block = $blocks_result->fetch_assoc()) {
        $blocks_data[] = $block;
    }
}

// =========================
// FETCH ALL FACILITIES WITH BLOCK INFO
// =========================
$facilities_sql = "SELECT facility_id, facility_name, block_id FROM facility WHERE status = 'Available' ORDER BY facility_name ASC";
$facilities_result = $conn->query($facilities_sql);
$facilities_data = [];
if ($facilities_result && $facilities_result->num_rows > 0) {
    while ($facility = $facilities_result->fetch_assoc()) {
        $facilities_data[] = $facility;
    }
}

// =========================
// NO NEED TO FETCH STUDENTS/STAFF - USING TEXT INPUT
// =========================
?>

<?php include("../page/header.php"); ?>

<main>
<style>
.edit-page {
    max-width: 800px;
    margin: 3rem auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    padding: 2rem;
}
.edit-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:.75rem;
}
.form-group label {
    font-weight:600;
    margin-bottom:6px;
    display:block;
}
.form-group input, .form-group select, .form-group textarea {
    width:100%;
    padding:.5rem .65rem;
    border-radius:8px;
    border:1px solid #e1e6f3;
}
.form-group textarea {
    resize: vertical;
    min-height: 80px;
}
.full-row{grid-column:1/-1;}
.btn-row{
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}
.btn-row .btn{border-radius:999px;}
.info-text {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 4px;
}
</style>

<div class="container">
<div class="edit-page">

<h3>Add Facility Booking</h3>

<form method="post" id="bookingForm">

<div class="edit-row">

<div class="form-group">
    <label>Building</label>
    <select name="building_id" id="buildingSelect" required>
        <option value="">-- Select Building --</option>
        <?php
        if ($buildings_result && $buildings_result->num_rows > 0) {
            while ($building = $buildings_result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($building['building_id']) . '">' 
                     . htmlspecialchars($building['building_name']) 
                     . '</option>';
            }
        }
        ?>
    </select>
</div>

<div class="form-group">
    <label>Block</label>
    <select name="block_id" id="blockSelect" required disabled>
        <option value="">-- Select Building First --</option>
    </select>
</div>

<div class="form-group full-row">
    <label>Facility</label>
    <select name="facility_id" id="facilitySelect" required disabled>
        <option value="">-- Select Block First --</option>
    </select>
</div>

<div class="form-group full-row">
    <label>Booked By</label>
    <select name="booked_by_type" id="bookedByType" required>
        <option value="">-- Select Type --</option>
        <option value="student">Student</option>
        <option value="staff">Staff</option>
    </select>
</div>

<div class="form-group full-row" id="studentGroup" style="display:none;">
    <label>Student ID</label>
    <input type="text" name="student_id" id="studentInput" placeholder="Enter Student ID (e.g. S001)">
</div>

<div class="form-group full-row" id="staffGroup" style="display:none;">
    <label>Staff ID</label>
    <input type="text" name="staff_id" id="staffInput" placeholder="Enter Staff ID (e.g. ST001)">
</div>

<div class="form-group">
    <label>Start Date</label>
    <input type="date" name="start_date" id="startDate" required>
</div>

<div class="form-group">
    <label>Start Time</label>
    <input type="time" name="start_time" id="startTime" required>
</div>

<div class="form-group">
    <label>End Date</label>
    <input type="date" name="end_date" id="endDate" required>
</div>

<div class="form-group">
    <label>End Time</label>
    <input type="time" name="end_time" id="endTime" required>
</div>

<div class="form-group full-row">
    <label>Notes</label>
    <textarea name="notes" placeholder="Enter any additional notes or special requirements..."></textarea>
</div>

<!-- Hidden status field - auto set to Pending -->
<input type="hidden" name="status" value="Pending">

</div>

<div class="btn-row">
<a href="booking_facility.php" class="btn btn-outline-secondary">Cancel</a>
<button type="submit" class="btn btn-success">Add Booking</button>
</div>

</form>

</div>
</div>

<script>
// Pass PHP data to JavaScript
const blocksData = <?php echo json_encode($blocks_data); ?>;
const facilitiesData = <?php echo json_encode($facilities_data); ?>;

// Handle booked by type selection
document.getElementById('bookedByType').addEventListener('change', function() {
    const type = this.value;
    const studentGroup = document.getElementById('studentGroup');
    const staffGroup = document.getElementById('staffGroup');
    const studentInput = document.getElementById('studentInput');
    const staffInput = document.getElementById('staffInput');
    
    // Hide both groups and reset values
    studentGroup.style.display = 'none';
    staffGroup.style.display = 'none';
    studentInput.value = '';
    staffInput.value = '';
    studentInput.removeAttribute('required');
    staffInput.removeAttribute('required');
    
    // Show the appropriate group
    if (type === 'student') {
        studentGroup.style.display = 'block';
        studentInput.setAttribute('required', 'required');
    } else if (type === 'staff') {
        staffGroup.style.display = 'block';
        staffInput.setAttribute('required', 'required');
    }
});

// Handle building selection
document.getElementById('buildingSelect').addEventListener('change', function() {
    const buildingId = this.value;
    const blockSelect = document.getElementById('blockSelect');
    const facilitySelect = document.getElementById('facilitySelect');
    
    // Clear current options
    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    facilitySelect.innerHTML = '<option value="">-- Select Block First --</option>';
    facilitySelect.disabled = true;
    
    if (buildingId === '') {
        blockSelect.disabled = true;
        return;
    }
    
    // Filter blocks by selected building
    const filteredBlocks = blocksData.filter(block => block.building_id === buildingId);
    
    if (filteredBlocks.length === 0) {
        blockSelect.innerHTML = '<option value="">-- No Blocks Available --</option>';
        blockSelect.disabled = true;
        return;
    }
    
    // Populate block dropdown
    filteredBlocks.forEach(block => {
        const option = document.createElement('option');
        option.value = block.block_id;
        option.textContent = block.block_name;
        blockSelect.appendChild(option);
    });
    
    blockSelect.disabled = false;
});

// Handle block selection
document.getElementById('blockSelect').addEventListener('change', function() {
    const blockId = this.value;
    const facilitySelect = document.getElementById('facilitySelect');
    
    // Clear current options
    facilitySelect.innerHTML = '<option value="">-- Select Facility --</option>';
    
    if (blockId === '') {
        facilitySelect.disabled = true;
        return;
    }
    
    // Filter facilities by selected block
    const filteredFacilities = facilitiesData.filter(facility => facility.block_id === blockId);
    
    if (filteredFacilities.length === 0) {
        facilitySelect.innerHTML = '<option value="">-- No Available Facilities --</option>';
        facilitySelect.disabled = true;
        return;
    }
    
    // Populate facility dropdown
    filteredFacilities.forEach(facility => {
        const option = document.createElement('option');
        option.value = facility.facility_id;
        option.textContent = facility.facility_name;
        facilitySelect.appendChild(option);
    });
    
    facilitySelect.disabled = false;
});

// Validate form before submission
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const bookedByType = document.getElementById('bookedByType').value;
    const studentId = document.getElementById('studentInput').value.trim();
    const staffId = document.getElementById('staffInput').value.trim();
    const startDate = document.getElementById('startDate').value;
    const startTime = document.getElementById('startTime').value;
    const endDate = document.getElementById('endDate').value;
    const endTime = document.getElementById('endTime').value;
    
    // Check if correct person is selected based on type
    if (bookedByType === 'student' && !studentId) {
        e.preventDefault();
        alert('Please enter a Student ID for this booking.');
        return;
    }
    
    if (bookedByType === 'staff' && !staffId) {
        e.preventDefault();
        alert('Please enter a Staff ID for this booking.');
        return;
    }
    
    // Validate date range
    const startDateTime = new Date(startDate + ' ' + startTime);
    const endDateTime = new Date(endDate + ' ' + endTime);
    
    if (startDateTime >= endDateTime) {
        e.preventDefault();
        alert('End date and time must be after start date and time.');
        return;
    }
    
    // Confirm submission
    if (!confirm('Are you sure you want to add this booking?')) {
        e.preventDefault();
    }
});

// Set minimum date to today
const today = new Date().toISOString().split('T')[0];
document.getElementById('startDate').min = today;
document.getElementById('endDate').min = today;
</script>

</main>

<?php include("../page/footer.php"); ?>