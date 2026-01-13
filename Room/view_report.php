<?php
include("../config/config.php");

/* ======================
   GET REPORT ID
====================== */
$report_id = $_GET['id'] ?? '';

if ($report_id === '') {
    header("Location: /workshop2/Room/report.php");
    exit;
}

/* ======================
   FETCH REPORT
====================== */
$sql = "SELECT 
            report_id,
            student_id,
            report_title,
            report_description,
            report_location,
            report_status,
            created_at
        FROM report
        WHERE report_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Report not found</h4></div>";
    exit;
}

$report = $result->fetch_assoc();

/* ======================
   Helper function
====================== */
function s($arr, $key, $default = '') {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key]) : $default;
}

$back_url = "/workshop2/Room/report.php";
?>

<?php include("../page/header.php"); ?>

<main>
<style>
html, body { background:#ffffff !important; margin:0; padding:0; }
.container-fluid { padding:2.5rem; }

.profile-shell {
    background:#fff;
    border-radius:14px;
    padding:2.25rem;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.profile-top {
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    flex-wrap:wrap;
    gap:15px;
}

.profile-name {
    font-size:1.6rem;
    font-weight:800;
}

.badge-status {
    padding:4px 12px;
    border-radius:8px;
    font-size:.75rem;
    font-weight:700;
    display:inline-block;
}

.badge-new {
    background:#e3f2fd;
    color:#1565c0;
}

.badge-progress {
    background:#fff8e1;
    color:#ff8f00;
}

.badge-resolved {
    background:#e7f7ee;
    color:#1e8e5a;
}

.badge-rejected {
    background:#fdecea;
    color:#c62828;
}

.profile-details {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:1.25rem 4rem;
    margin-top:2rem;
}

.detail-title {
    font-size:.75rem;
    font-weight:800;
    color:#2a2a8c;
    text-transform:uppercase;
}

.detail-value {
    font-weight:600;
}

.student-back {
    margin-bottom:1rem;
}

.full-row { grid-column:1/-1; }

.edit-btn {
    white-space:nowrap;
}
</style>

<div class="container-fluid">

    <div class="student-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to Report List
        </a>
    </div>

    <div class="profile-shell">

        <!-- ===== TOP ===== -->
        <div class="profile-top">

            <div>
                <div class="profile-name">
                    <?= s($report,'report_title') ?>
                </div>

                <?php
                $status = $report['report_status'];
                $statusClass = match ($status) {
                    'New'         => 'badge-new',
                    'In Progress' => 'badge-progress',
                    'Resolved'    => 'badge-resolved',
                    'Rejected'    => 'badge-rejected',
                    default       => 'badge-new'
                };
                ?>

                <div class="badges mt-2">
                    <span class="badge-status <?= $statusClass ?>">
                        <?= s($report,'report_status') ?>
                    </span>
                </div>

                <div class="text-muted mt-2">
                    Student ID: <?= s($report,'student_id') ?>
                </div>

                <div class="text-muted mt-1">
                    Created at: <?= date('d M Y, h:i A', strtotime($report['created_at'])) ?>
                </div>
            </div>

            <div>
                <a href="edit_report.php?id=<?= s($report,'report_id') ?>" class="btn btn-primary edit-btn">
                    Edit Report
                </a>
            </div>

        </div>

        <!-- ===== DETAILS ===== -->
        <div class="profile-details">

            <div>
                <div class="detail-title">Report ID</div>
                <div class="detail-value"><?= s($report,'report_id') ?></div>
            </div>

            <div>
                <div class="detail-title">Student ID</div>
                <div class="detail-value"><?= s($report,'student_id') ?></div>
            </div>

            <div class="full-row">
                <div class="detail-title">Location</div>
                <div class="detail-value"><?= s($report,'report_location') ?: '—' ?></div>
            </div>

            <div class="full-row">
                <div class="detail-title">Description</div>
                <div class="detail-value"><?= nl2br(s($report,'report_description')) ?: '—' ?></div>
            </div>

        </div>

    </div>
</div>
</main>

<?php include("../page/footer.php"); ?>
