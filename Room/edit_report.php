<?php
include("../config/config.php");

/* =========================
   UPDATE REPORT (POST)
   Only status & description
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $report_id          = $_POST['report_id'] ?? '';
    $report_description = $_POST['report_description'] ?? '';
    $report_status      = $_POST['report_status'] ?? '';

    // Safety check
    if ($report_id === '') {
        header("Location: view_report.php");
        exit;
    }

    $sql = "UPDATE report SET
                report_description = ?,
                report_status = ?
            WHERE report_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssi",
        $report_description,
        $report_status,
        $report_id
    );
    $stmt->execute();

    header("Location: view_report.php?id=" . $report_id);
    exit;
}

/* =========================
   GET REPORT DATA (DISPLAY)
========================= */
$report_id = $_GET['id'] ?? '';

if ($report_id === '') {
    header("Location: view_report.php");
    exit;
}

$sql = "SELECT * FROM report WHERE report_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    header("Location: view_report.php");
    exit;
}

function s($k){
    global $report;
    return htmlspecialchars($report[$k] ?? '');
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
.form-group input,
.form-group select,
.form-group textarea {
    width:100%;
    padding:.5rem .65rem;
    border-radius:8px;
    border:1px solid #e1e6f3;
}
.form-group textarea {
    min-height:120px;
}
.form-group input:disabled {
    background:#f5f5f5;
    color:#6c757d;
}
.full-row {
    grid-column:1/-1;
}
.btn-row {
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}
.info-box {
    background:#f8f9fa;
    border-radius:8px;
    padding:1rem;
    margin-bottom:1rem;
}
@media (max-width:768px){
    .edit-row{grid-template-columns:1fr;}
}
</style>

<div class="container">
<div class="edit-page">

<h3>Edit Report</h3>

<div class="info-box">
    <strong>Report ID:</strong> <?= s('report_id') ?> |
    <strong>Created:</strong> <?= date('d M Y, h:i A', strtotime($report['created_at'])) ?>
</div>

<form method="post" id="editForm">

<input type="hidden" name="report_id" value="<?= s('report_id') ?>">

<div class="edit-row">

<div class="form-group">
<label>Student ID</label>
<input type="text" value="<?= s('student_id') ?>" disabled>
</div>

<div class="form-group">
<label>Report Status</label>
<select name="report_status" required>
    <option value="New" <?= s('report_status')=='New'?'selected':'' ?>>New</option>
    <option value="In Progress" <?= s('report_status')=='In Progress'?'selected':'' ?>>In Progress</option>
    <option value="Resolved" <?= s('report_status')=='Resolved'?'selected':'' ?>>Resolved</option>
    <option value="Rejected" <?= s('report_status')=='Rejected'?'selected':'' ?>>Rejected</option>
</select>
</div>

<div class="form-group full-row">
<label>Report Title</label>
<input type="text" value="<?= s('report_title') ?>" disabled>
</div>

<div class="form-group full-row">
<label>Report Location</label>
<input type="text" value="<?= s('report_location') ?>" disabled>
</div>

<div class="form-group full-row">
<label>Report Description</label>
<textarea name="report_description" required><?= s('report_description') ?></textarea>
</div>

</div>

<div class="btn-row">
<a href="view_report.php?id=<?= s('report_id') ?>" class="btn btn-outline-secondary">Cancel</a>
<button type="submit" class="btn btn-success">Save Changes</button>
</div>

</form>

</div>
</div>

<script>
document.getElementById('editForm').addEventListener('submit', function(e){
    if(!confirm('Are you sure you want to save these changes?')){
        e.preventDefault();
    }
});
</script>

</main>

<?php include("../page/footer.php"); ?>
