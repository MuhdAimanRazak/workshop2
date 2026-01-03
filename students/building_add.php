<?php
include("../page/header.php");
include("../config/config.php");
?>

<main>
<style>
.form-card {
    max-width: 500px;
    margin: 3rem auto;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0,0,0,.12);
}
</style>

<div class="container">

    <div class="form-card bg-white">
        <h4 class="fw-bold mb-4 text-center">Add New Building</h4>

        <form method="POST" action="building_add_process.php">

            <!-- Building Name -->
            <div class="mb-3">
                <label class="form-label">Building Name</label>
                <input type="text"
                       name="building_name"
                       class="form-control"
                       required>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <a href="building_all.php"
                   class="btn btn-secondary rounded-pill px-4">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-success rounded-pill px-4">
                    Save Building
                </button>
            </div>

        </form>
    </div>

</div>
</main>

<?php include("../page/footer.php"); ?>
