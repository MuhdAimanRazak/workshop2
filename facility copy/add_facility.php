<?php
include("../config/config.php");

/* =========================
   SAVE DATA (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =========================
    // FORM DATA
    // =========================
    $block_id      = trim($_POST['block_id']);
    $facility_name = trim($_POST['facility_name']);
    $status        = 'Available'; // Auto set to Available

    // =========================
    // INSERT DATA (facility_id AUTO INCREMENT)
    // =========================
    $sql = "INSERT INTO facility (
                block_id,
                facility_name,
                status
            ) VALUES (?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sss",
        $block_id,
        $facility_name,
        $status
    );

    $stmt->execute();

    // Get the auto-generated facility_id
    $facility_id = $conn->insert_id;

    echo "<script>
        alert('Facility successfully added!');
        window.location.href = 'facility_details.php?id=$facility_id';
    </script>";
    exit;
}

// =========================
// FETCH BUILDINGS FOR DROPDOWN
// =========================
$buildings_sql = "SELECT building_id, building_name FROM building ORDER BY building_name ASC";
$buildings_result = $conn->query($buildings_sql);

// =========================
// FETCH ALL BLOCKS WITH BUILDING INFO (FOR JAVASCRIPT)
// =========================
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
</style>

<div class="container">
<div class="edit-page">

<h3>Add Facility</h3>

<form method="post" id="facilityForm">

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
    <label>Facility Name</label>
    <input type="text" name="facility_name" required placeholder="e.g. Computer Laboratory 1">
</div>

</div>

<div class="btn-row">
<a href="facility.php" class="btn btn-outline-secondary">Cancel</a>
<button type="submit" class="btn btn-success">Add Facility</button>
</div>

</form>

</div>
</div>

<script>
// Pass PHP blocks data to JavaScript
const blocksData = <?php echo json_encode($blocks_data); ?>;

// Handle building selection
document.getElementById('buildingSelect').addEventListener('change', function() {
    const buildingId = this.value;
    const blockSelect = document.getElementById('blockSelect');
    
    // Clear current options
    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
    
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

// Form submission confirmation
document.getElementById("facilityForm").addEventListener("submit", function (e) {
    if (!confirm("Are you sure you want to add this facility?")) {
        e.preventDefault();
    }
});
</script>

</main>

<?php include("../page/footer.php"); ?>