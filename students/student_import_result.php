<?php
session_start();

/* =========================
   SAFE SESSION FETCH
========================= */
$success = $_SESSION['import_success'] ?? [];
$skipped = $_SESSION['import_skipped'] ?? [];

if (!is_array($success)) $success = [];
if (!is_array($skipped)) $skipped = [];
?>

<?php include("../page/header.php"); ?>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}
th, td {
    padding: .75rem;
    border: 1px solid #ccc;
    text-align: left;
}
th {
    background: #f5f5f5;
}
.section-title {
    margin-top: 2rem;
    font-size: 1.3rem;
    font-weight: 700;
}
.result-summary {
    margin-bottom: 1.5rem;
}
.download-btn {
    margin-top: 1.5rem;
}

/* ✅ SCROLLABLE TABLE (LIKE STUDENT PAGE) */
.result-table-wrapper {
    max-height: 420px;
    overflow: auto;
    margin-top: 1rem;
}



/* ===== MATCH STUDENT PAGE COLOR STYLE ===== */
.result-table-wrapper .table th,
.result-table-wrapper .table td {
    background-color: #ffffffff !important;
    vertical-align: middle;
}

.result-table-wrapper .table thead {
    background-color: #ffffff !important;
}

/* ===== BLUE TABLE HEADER ===== */
.result-table-wrapper thead th {
    background-color: #5f6dff !important;  /* same blue as student page buttons */
    color: #8cd0f5ff !important;
    font-weight: 700;
}

/* Center download button */
.download-btn {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}



</style>

<main class="container-fluid" style="padding:2rem;">

    <a href="students_import_form.php" class="text-decoration-none">
        ← Back to Import Form
    </a>

    <h2 style="margin-top:1rem;">Import Result</h2>

    <div class="result-summary">
        <p><strong><?= count($success) ?></strong> students imported successfully.</p>
        <p><strong><?= count($skipped) ?></strong> records skipped.</p>
    </div>

    <!-- SUCCESS TABLE -->
    <div class="section-title">Successfully Added Students</div>

    <?php if (count($success) > 0): ?>
        <div class="table-responsive result-table-wrapper">
            <table class="table table-bordered align-middle">
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                </tr>

                <?php foreach ($success as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_id'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['full_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p style="color:#666;">No students were added.</p>
    <?php endif; ?>

    <!-- SKIPPED TABLE -->
    <div class="section-title">Skipped Records</div>

    <?php if (count($skipped) > 0): ?>
        <div class="table-responsive result-table-wrapper">
            <table class="table table-bordered align-middle">
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Reason</th>
                </tr>

                <?php foreach ($skipped as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_id'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['full_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['reason'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- DOWNLOAD SKIPPED CSV -->
        <form action="download_skipped_csv.php" method="post" class="download-btn">
            <button type="submit" class="btn btn-primary">
                ⬇ Download Skipped Records (CSV)
            </button>
        </form>

    <?php else: ?>
        <p style="color:#666;">No skipped records.</p>
    <?php endif; ?>

</main>

<?php include("../page/footer.php"); ?>
