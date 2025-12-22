<?php
include("../config/config.php");

/* ======================
   GET STUDENT ID (STRING)
====================== */
$student_id = $_GET['id'] ?? '';

// If invalid ID → back to student list
if ($student_id === '') {
    header("Location: /workshop2/students/student.php");
    exit;
}

/* ======================
   FETCH STUDENT
====================== */
$sql = "SELECT 
            student_id,
            full_name,
            email,
            address,
            phone_no,
            student_ic,
            faculty,
            course,
            parent_contact
        FROM student
        WHERE student_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error");
}

$roomSql = "
SELECT
    bld.building_name,
    blk.block_name,
    r.no_house,
    r.room_no,
    r.level,
    bkg.bed_no,
    bkg.status_payment
FROM booking bkg
JOIN room r       ON bkg.room_id = r.room_id
JOIN block blk    ON r.block_id = blk.block_id
JOIN building bld ON blk.building_id = bld.building_id
WHERE bkg.student_id = ?
LIMIT 1
";

$roomStmt = $conn->prepare($roomSql);
$roomStmt->bind_param("s", $student_id);
$roomStmt->execute();
$roomResult = $roomStmt->get_result();
$roomInfo = $roomResult->fetch_assoc();
$roomStmt->close();


/* 🔴 student_id IS STRING */
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Student not found</h4></div>";
    exit;
}

$student = $result->fetch_assoc();

function s($arr, $key, $default = '') {
    return isset($arr[$key]) ? $arr[$key] : $default;
}

$back_url = "/workshop2/students/student.php";
?>

<?php include("../page/header.php"); ?>

<main>
<style>
/* ===== UI NOT TOUCHED ===== */
.container-fluid { padding: 2.5rem; }
.profile-shell {
    background:#fff;
    border-radius:12px;
    padding:2.25rem;
    box-shadow:0 8px 25px rgba(0,0,0,.05);
}
.profile-top {
    display:flex;
    align-items:center;
    position:relative;
}
.avatar-wrap {
    width:120px;
    height:120px;
    border-radius:50%;
    background:#e6eef8;
    overflow:hidden;
}
.avatar-wrap img {
    width:100%;
    height:100%;
    object-fit:cover;
}
.profile-name {
    font-size:1.25rem;
    font-weight:800;
    margin-left:20px;
}
.edit-button-wrap {
    position:absolute;
    height: 42px;
    right:0;
    top:10px;
}

.change-pass-btn {
    white-space: nowrap;
    min-width: 160px;
    background: #4f7cff;
    border-color: #4f7cff;
    color: #fff;
}
.change-pass-btn:hover {
    background: #3b67f2;
}
.profile-details {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:1rem 2rem;
    margin-top:1.5rem;
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
    margin-bottom:.75rem;
}

.payment-badge {
    display: inline-block;
    padding: .25rem .65rem;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.payment-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.payment-badge.paid {
    background: #d4edda;
    color: #155724;
}

.payment-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}
.full-row {
    grid-column: 1 / -1;
}

.hostel-grid-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem 2rem;
    margin-top: .75rem;
}

/* ===== HOSTEL CARD ===== */
.hostel-card {
    margin-top: 1.25rem;
    padding: 1.25rem 1.5rem;
    background: #f8faff;
    border: 1px solid #e3e9ff;
    border-radius: 12px;
}

.hostel-card .detail-title {
    margin-bottom: .75rem;
}

.hostel-grid-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem 2rem;
}

/* responsive */
@media (max-width: 768px) {
    .hostel-grid-info {
        grid-template-columns: 1fr;
    }
}


</style>

<div class="container-fluid">

    <div class="student-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to list
        </a>
    </div>

    <div class="profile-shell">

        <div class="profile-top">
            <div class="avatar-wrap">
                <img src="/workshop2/assets/avatar-default.png">
            </div>

            <div class="profile-name">
                <?= htmlspecialchars(s($student,'full_name')) ?>
            </div>

<div class="edit-button-wrap d-flex gap-2">

    <a href="editstudentprofile.php?id=<?= s($student,'student_id') ?>"
       class="btn btn-primary">
        Edit
    </a>

    <a href="change_student_password.php?id=<?= s($student,'student_id') ?>"
       class="btn btn-primary change-pass-btn">
        Change Password
    </a>

    <form method="post" action="delete_student.php"
          onsubmit="return confirm('Are you sure you want to delete this student?');">
        <input type="hidden" name="student_id" value="<?= s($student,'student_id') ?>">
        <button type="submit" class="btn btn-danger">
            Delete
        </button>
    </form>

</div>


        </div>

        <div class="profile-details">

            <div>
                <div class="detail-title">Matric Number</div>
                <div class="detail-value"><?= s($student,'student_id') ?></div>
            </div>

            <div>
                <div class="detail-title">Phone</div>
                <div class="detail-value"><?= s($student,'phone_no') ?></div>
            </div>

            <div>
                <div class="detail-title">IC</div>
                <div class="detail-value"><?= s($student,'student_ic') ?></div>
            </div>

            <div>
                <div class="detail-title">Address</div>
                <div class="detail-value"><?= nl2br(s($student,'address')) ?></div>
            </div>

            <div>
                <div class="detail-title">Faculty</div>
                <div class="detail-value"><?= s($student,'faculty') ?></div>
            </div>


            <div>
                <div class="detail-title">Course</div>
                <div class="detail-value"><?= s($student,'course') ?></div>
            </div>

            <div>
                <div class="detail-title">Parent Contact</div>
                <div class="detail-value"><?= s($student,'parent_contact') ?></div>
            </div>

            <div>
                <div class="detail-title">Email</div>
                <div class="detail-value"><?= s($student,'email') ?: '—' ?></div>
            </div>

<div class="hostel-wrapper full-row hostel-card">
    <div class="detail-title">Hostel Information</div>

    <div class="hostel-grid-info">
        <div>
            <div class="detail-title">Building</div>
            <div class="detail-value"><?= $roomInfo['building_name'] ?? '—' ?></div>
        </div>
<div>
    <div class="detail-title">
        <?= !empty($roomInfo['no_house']) ? 'House / Block' : 'Block' ?>
    </div>

<div class="detail-value">
    <?= htmlspecialchars($roomInfo['block_name'] ?? '—') ?>
    <?php if (!empty($roomInfo['no_house'])): ?>
        &nbsp;–&nbsp;<?= htmlspecialchars($roomInfo['no_house']) ?>
    <?php endif; ?>
</div>

</div>


        <div>
            <div class="detail-title">Level</div>
            <div class="detail-value"><?= $roomInfo['level'] ?? '—' ?></div>
        </div>

        <div>
            <div class="detail-title">Room</div>
            <div class="detail-value"><?= $roomInfo['room_no'] ?? '—' ?></div>
        </div>

        <div>
            <div class="detail-title">Bed</div>
            <div class="detail-value"><?= $roomInfo['bed_no'] ?? '—' ?></div>
        </div>

        <div>
<div class="detail-title">Payment Status</div>
<div class="detail-value">
    <?php if (empty($roomInfo) || empty($roomInfo['status_payment'])): ?>
        <span class="payment-badge dash">—</span>
    <?php else: ?>
        <span class="payment-badge <?= strtolower($roomInfo['status_payment']) ?>">
            <?= htmlspecialchars($roomInfo['status_payment']) ?>
        </span>
    <?php endif; ?>
</div>

        </div>
    </div>
</div>

</div>
</main>

<?php include("../page/footer.php"); ?>
