<?php
include("../config/config.php");

/* =========================
   HANDLE UPDATE (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $facility_id   = $_POST['facility_id'];
    $block_id      = $_POST['block_id'];
    $facility_name = $_POST['facility_name'];
    $status        = $_POST['status'];
    $start_date    = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
    $end_date      = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;

    // If Available → clear dates
    if ($status === 'Available') {
        $start_date = NULL;
        $end_date   = NULL;
    }

    /* =========================
       UPDATE FACILITY
    ========================= */
    $sql = "UPDATE facility SET
                block_id = ?,
                facility_name = ?,
                status = ?,
                start_date = ?,
                end_date = ?
            WHERE facility_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssi",
        $block_id,
        $facility_name,
        $status,
        $start_date,
        $end_date,
        $facility_id
    );
    $stmt->execute();

    /* ======================================
       AUTO-CANCEL OVERLAPPING BOOKINGS
    ====================================== */
    if ($status !== 'Available' && $start_date && $end_date) {

        // Find overlapping bookings
        $bookingSql = "
            SELECT booking_facility_id
            FROM booking_facility
            WHERE facility_id = ?
              AND status != 'Cancelled'
              AND start_date <= ?
              AND end_date >= ?
        ";

        $bookingStmt = $conn->prepare($bookingSql);
        $bookingStmt->bind_param(
            "iss",
            $facility_id,
            $end_date,     // booking_start <= facility_end
            $start_date    // booking_end >= facility_start
        );
        $bookingStmt->execute();
        $bookingResult = $bookingStmt->get_result();

        // Cancel overlapping bookings
        if ($bookingResult->num_rows > 0) {

            $cancelSql = "
                UPDATE booking_facility
                SET status = 'Cancelled'
                WHERE booking_facility_id = ?
            ";
            $cancelStmt = $conn->prepare($cancelSql);

            while ($booking = $bookingResult->fetch_assoc()) {
                $cancelStmt->bind_param("i", $booking['booking_facility_id']);
                $cancelStmt->execute();
            }
        }
    }

    header("Location: facility_details.php?id=" . $facility_id);
    exit;
}

/* =========================
   GET DATA (DISPLAY)
========================= */
$facility_id = $_GET['id'] ?? '';

$sql = "SELECT f.*, b.building_id 
        FROM facility f
        INNER JOIN block b ON f.block_id = b.block_id
        WHERE f.facility_id = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $facility_id);
$stmt->execute();
$facility = $stmt->get_result()->fetch_assoc();

function s($k){
    global $facility;
    return htmlspecialchars($facility[$k] ?? '');
}

/* =========================
   FETCH BUILDINGS
========================= */
$buildings_sql = "SELECT building_id, building_name FROM building ORDER BY building_name ASC";
$buildings_result = $conn->query($buildings_sql);

/* =========================
   FETCH BLOCKS
========================= */
$blocks_sql = "SELECT block_id, block_name, building_id FROM block ORDER BY block_name ASC";
$blocks_result = $conn->query($blocks_sql);
$blocks_data = [];

if ($blocks_result && $blocks_result->num_rows > 0) {
    while ($block = $blocks_result->fetch_assoc()) {
        $blocks_data[] = $block;
    }
}
?>

<?php include("../page/header.php"); ?>

<main>

<style>

    html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}
.edit-page {
    max-width: 1100px;
    margin: 3.25rem auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0,0,0,.06);
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
.form-group input, .form-group select {
    width:100%;
    padding:.5rem .65rem;
    border-radius:8px;
    border:1px solid #e1e6f3;
}
.full-row{grid-column:1/-1;}
.btn-row{
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}
.btn-row .btn{border-radius:999px;}
.date-fields { display:none; }
.date-fields.show { display:contents; }
</style>

<div class="container">
<div class="edit-page">

<h3>Edit Facility</h3>

<form method="post">

<input type="hidden" name="facility_id" value="<?= s('facility_id') ?>">

<div class="edit-row">

<div class="form-group full-row">
<label>Facility Name</label>
<input type="text" name="facility_name" value="<?= s('facility_name') ?>" required>
</div>

<div class="form-group">
<label>Building</label>
<select name="building_id" id="buildingSelect" required>
<option value="">-- Select Building --</option>
<?php while ($b = $buildings_result->fetch_assoc()): ?>
<option value="<?= $b['building_id'] ?>" <?= ($b['building_id']==$facility['building_id'])?'selected':'' ?>>
<?= htmlspecialchars($b['building_name']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="form-group">
<label>Block</label>
<select name="block_id" id="blockSelect" required></select>
</div>

<div class="form-group full-row">
<label>Status</label>
<select name="status" id="statusSelect" required>
<option value="Available" <?= (s('status')=='Available')?'selected':'' ?>>Available</option>
<option value="Occupied" <?= (s('status')=='Occupied')?'selected':'' ?>>Occupied</option>
<option value="Maintenance" <?= (s('status')=='Maintenance')?'selected':'' ?>>Maintenance</option>
</select>
</div>

<div class="form-group date-fields" id="dateFields">
<label>Start Date</label>
<input type="date" name="start_date" id="startDate" value="<?= s('start_date') ?>">
</div>

<div class="form-group date-fields">
<label>End Date</label>
<input type="date" name="end_date" id="endDate" value="<?= s('end_date') ?>">
</div>

</div>

<div class="btn-row">
<a href="facility_details.php?id=<?= s('facility_id') ?>" class="btn btn-outline-secondary">Cancel</a>
<button type="submit" class="btn btn-success">Save</button>
</div>

</form>
</div>
</div>

<script>
const blocksData = <?= json_encode($blocks_data); ?>;
const currentBlockId = "<?= s('block_id') ?>";

function populateBlocks(buildingId, selectedBlockId=null){
    const blockSelect = document.getElementById('blockSelect');
    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';

    const filtered = blocksData.filter(b => b.building_id === buildingId);

    filtered.forEach(b=>{
        const o = document.createElement('option');
        o.value = b.block_id;
        o.textContent = b.block_name;
        if (b.block_id === selectedBlockId) o.selected = true;
        blockSelect.appendChild(o);
    });
}

document.getElementById('buildingSelect').addEventListener('change', e=>{
    populateBlocks(e.target.value);
});

populateBlocks(
    document.getElementById('buildingSelect').value,
    currentBlockId
);

// Status toggle
function toggleDates(){
    const status = document.getElementById('statusSelect').value;
    document.querySelectorAll('.date-fields')
        .forEach(f => f.classList.toggle('show', status !== 'Available'));
}
document.getElementById('statusSelect').addEventListener('change', toggleDates);
toggleDates();
</script>

</main>

<?php include("../page/footer.php"); ?>
