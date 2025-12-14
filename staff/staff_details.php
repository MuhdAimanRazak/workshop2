<?php
include("../config/config.php");

/* ======================
   GET STAFF ID (STRING)
====================== */
$staff_id = $_GET['id'] ?? '';

if ($staff_id === '') {
    header("Location: /workshop2/staff/staff.php");
    exit;
}

/* ======================
   FETCH STAFF
====================== */
$sql = "SELECT 
            staff_id,
            full_name,
            email,
            address,
            phone_no,
            staff_ic,
            role
        FROM staff
        WHERE staff_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error");
}

$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Staff not found</h4></div>";
    exit;
}

$staff = $result->fetch_assoc();

function s($arr, $key, $default = '') {
    return isset($arr[$key]) ? $arr[$key] : $default;
}

$back_url = "/workshop2/staff/staff.php";
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
    height:42px;
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
    gap:1rem 4rem;
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
</style>

<div class="container-fluid">

    <div class="student-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to list
        </a>
    </div>

    <div class="profile-shell">

        <!-- ===== TOP ===== -->
        <div class="profile-top">

            <div class="avatar-wrap">
                <!-- Default avatar (same pattern as student) -->
                <img src="/workshop2/assets/avatar-default.png">
            </div>

            <div class="profile-name">
                <?= htmlspecialchars(s($staff,'full_name')) ?>
            </div>

  <div class="edit-button-wrap d-flex gap-2">
    <a href="edit_staff.php?id=<?= s($staff,'staff_id') ?>" class="btn btn-primary">
        Edit
    </a>

<a href="change_staff_password.php?id=<?= s($staff,'staff_id') ?>"
   class="btn btn-secondary change-pass-btn">
    Change Password
</a>


    <form method="post" action="delete_staff.php"
          onsubmit="return confirm('Are you sure you want to delete this staff?');">
        <input type="hidden" name="staff_id" value="<?= s($staff,'staff_id') ?>">
        <button type="submit" class="btn btn-danger">
            Delete
        </button>
    </form>
</div>


        </div>

        <!-- ===== DETAILS ===== -->
        <div class="profile-details">

            <div>
                <div class="detail-title">Staff ID</div>
                <div class="detail-value"><?= s($staff,'staff_id') ?></div>
            </div>

            <div>
                <div class="detail-title">Phone</div>
                <div class="detail-value"><?= s($staff,'phone_no') ?: '—' ?></div>
            </div>

            <div>
                <div class="detail-title">IC</div>
                <div class="detail-value"><?= s($staff,'staff_ic') ?: '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Address</div>
                <div class="detail-value"><?= nl2br(s($staff,'address')) ?: '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Role</div>
                <div class="detail-value"><?= s($staff,'role') ?: '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Email</div>
                <div class="detail-value"><?= s($staff,'email') ?: '—' ?></div>
            </div>

        </div>

    </div>
</div>
</main>

<?php include("../page/footer.php"); ?>
