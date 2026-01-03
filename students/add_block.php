<?php
include("../config/config.php");

/* ===== GET BUILDING ID FROM URL ===== */
$building_id = $_GET['building_id'] ?? 0;
if ($building_id == 0) {
    die("Invalid building.");
}

/* ===== GET BUILDING NAME ===== */
$sql = "SELECT building_name FROM building WHERE building_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$building = $stmt->get_result()->fetch_assoc();

if (!$building) {
    die("Building not found.");
}

$building_name = $building['building_name'];

include("../page/header.php");
?>

<main>

<style>
.form-card {
    max-width: 520px;
    margin: 3rem auto;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0,0,0,.12);
}
</style>

<div class="container">

    <div class="form-card bg-white">
        <h4 class="fw-bold mb-2 text-center">Add Block</h4>
        <p class="text-center text-muted mb-4">
            Building: <strong><?= htmlspecialchars($building_name) ?></strong>
        </p>

        <form method="POST" action="add_block_process.php">

            <input type="hidden" name="building_id" value="<?= $building_id ?>">

            <!-- Block Name -->
            <div class="mb-3">
                <label class="form-label">Block Name</label>
                <input type="text" name="block_name" class="form-control" required>
            </div>

            <!-- Total Level -->
            <div class="mb-3">
                <label class="form-label">Total Level</label>
                <input type="number" name="total_level" class="form-control" min="1" required>
            </div>

            <!-- Gender -->
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="student_gender" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <!-- Person In Charge -->
            <div class="mb-4">
                <label class="form-label">Person In Charge</label>
                <input type="text" name="person_in_charge" class="form-control">
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <a href="manage_blocks.php?id=<?= $building_id ?>"
                   class="btn btn-secondary rounded-pill px-4">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-success rounded-pill px-4">
                    Save Block
                </button>
            </div>

        </form>
    </div>

</div>
</main>

<?php include("../page/footer.php"); ?>
