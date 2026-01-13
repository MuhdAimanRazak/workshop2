<?php
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $facility_id   = $_POST['facility_id'];
    $block_id      = $_POST['block_id'];
    $facility_name = $_POST['facility_name'];
    $status        = $_POST['status'];
    $start_date    = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
    $end_date      = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;

    // If status is Available, clear dates
    if ($status === 'Available') {
        $start_date = NULL;
        $end_date = NULL;
    }

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

    header("Location: facility_details.php?id=".$facility_id);
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

function s($k){ global $facility; return htmlspecialchars($facility[$k] ?? ''); }

// Fetch buildings for dropdown
$buildings_sql = "SELECT building_id, building_name FROM building ORDER BY building_name ASC";
$buildings_result = $conn->query($buildings_sql);

// Fetch all blocks with building info
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
/* ===== UI AS IS ===== */
.edit-page {
    max-width: 1100px;
    margin: 3.25rem auto;
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
.date-fields {
    display: none;
}
.date-fields.show {
    display: contents;
}
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
        <?php
        if ($buildings_result && $buildings_result->num_rows > 0) {
            while ($building = $buildings_result->fetch_assoc()) {
                $selected = ($building['building_id'] == $facility['building_id']) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($building['building_id']) . '" ' . $selected . '>' 
                     . htmlspecialchars($building['building_name']) 
                     . '</option>';
            }
        }
        ?>
    </select>
</div>

<div class="form-group">
    <label>Block</label>
    <select name="block_id" id="blockSelect" required>
        <option value="">-- Select Block --</option>
    </select>
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

<div class="form-group date-fields" id="dateFields2">
<label>End Date</label>
<input type="date" name="end_date" id="endDate" value="<?= s('end_date') ?>">
</div>

</div>

<div class="btn-row">
<a href="facility_details.php?id=<?= s('facility_id') ?>" 
   class="btn btn-outline-secondary">
   Cancel
</a>

<button type="submit" class="btn btn-success">Save</button>
</div>

</form>
</div>
</div>

<script>
// Pass PHP data to JavaScript
const blocksData = <?php echo json_encode($blocks_data); ?>;
const currentBlockId = "<?= s('block_id') ?>";
const currentStatus = "<?= s('status') ?>";

// Populate blocks based on selected building
function populateBlocks(buildingId, selectedBlockId = null) {
    const blockSelect = document.getElementById('blockSelect');
    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    
    if (!buildingId) {
        blockSelect.disabled = true;
        return;
    }
    
    const filteredBlocks = blocksData.filter(block => block.building_id === buildingId);
    
    if (filteredBlocks.length === 0) {
        blockSelect.innerHTML = '<option value="">-- No Blocks Available --</option>';
        blockSelect.disabled = true;
        return;
    }
    
    filteredBlocks.forEach(block => {
        const option = document.createElement('option');
        option.value = block.block_id;
        option.textContent = block.block_name;
        if (selectedBlockId && block.block_id === selectedBlockId) {
            option.selected = true;
        }
        blockSelect.appendChild(option);
    });
    
    blockSelect.disabled = false;
}

// Handle building selection change
document.getElementById('buildingSelect').addEventListener('change', function() {
    populateBlocks(this.value);
});

// Initialize blocks on page load
const initialBuildingId = document.getElementById('buildingSelect').value;
if (initialBuildingId) {
    populateBlocks(initialBuildingId, currentBlockId);
}

// Handle status change - show/hide date fields
function toggleDateFields() {
    const status = document.getElementById('statusSelect').value;
    const dateFields = document.querySelectorAll('.date-fields');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    
    if (status === 'Available') {
        dateFields.forEach(field => field.classList.remove('show'));
        startDate.removeAttribute('required');
        endDate.removeAttribute('required');
        startDate.value = '';
        endDate.value = '';
    } else {
        dateFields.forEach(field => field.classList.add('show'));
        startDate.setAttribute('required', 'required');
        endDate.setAttribute('required', 'required');
    }
}

// Initialize date fields visibility
document.getElementById('statusSelect').addEventListener('change', toggleDateFields);

// Set initial state on page load
toggleDateFields();
</script>

</main>

<?php include("../page/footer.php"); ?>