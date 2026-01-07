<?php
include("../config/config.php");

/* ======================
   GET STAFF ID
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

/* Mask IC */
function mask_ic($ic) {
    if (!$ic) return '—';
    return substr($ic, 0, 10) . '****';
}

$back_url = "/workshop2/staff/staff.php";
?>

<?php include("../page/header.php"); ?>

<main>
<style>
    /* ===============================
   FORCE FULL WHITE BACKGROUND
================================ */
html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}
.container-fluid { padding:2.5rem; }

.profile-shell {
    background:#fff;
    border-radius:14px;
    padding:2.25rem;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.profile-top {
    display:flex;
    align-items:center;
    position:relative;
    gap:20px;
}

.avatar-wrap {
    width:110px;
    height:110px;
    border-radius:50%;
    background:#e6eef8;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:42px;
    font-weight:700;
    color:#4f7cff;
}

.profile-name-wrap {
    display:flex;
    flex-direction:column;
}

.profile-name {
    font-size:1.5rem;
    font-weight:800;
}

.badges {
    margin-top:4px;
    display:flex;
    gap:8px;
}

.badge-role {
    background:#e8edff;
    color:#3b5bfd;
    padding:4px 10px;
    border-radius:8px;
    font-size:.75rem;
    font-weight:700;
}

.badge-active {
    background:#e7f7ee;
    color:#1e8e5a;
    padding:4px 10px;
    border-radius:8px;
    font-size:.75rem;
    font-weight:700;
}

.edit-button-wrap {
    position:absolute;
    right:0;
    top:0;
    display:flex;
    gap:10px;
}

.change-pass-btn {
    white-space:nowrap;
    min-width:160px;
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
</style>

<div class="container-fluid">

    <div class="student-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to Staff Directory
        </a>
    </div>

    <div class="profile-shell">

        <!-- ===== TOP ===== -->
        <div class="profile-top">

            <div class="avatar-wrap">
                <?= strtoupper(substr(s($staff,'full_name'),0,1)) ?>
            </div>

            <div class="profile-name-wrap">
                <div class="profile-name">
                    <?= htmlspecialchars(s($staff,'full_name')) ?>
                </div>

                <div class="badges">
                    <span class="badge-role"><?= ucfirst(s($staff,'role')) ?></span>
                    <span class="badge-active">Active</span>
                </div>

                <div class="text-muted mt-1">
                    <?= s($staff,'staff_id') ?>
                </div>
            </div>

            <div class="edit-button-wrap">
                <a href="edit_staff.php?id=<?= s($staff,'staff_id') ?>" class="btn btn-primary">
                    Edit
                </a>

                <a href="change_staff_password.php?id=<?= s($staff,'staff_id') ?>"
                   class="btn btn-outline-primary change-pass-btn">
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
                <div class="detail-value"><?= mask_ic(s($staff,'staff_ic')) ?></div>
            </div>

            <div>
                <div class="detail-title">Email</div>
                <div class="detail-value"><?= s($staff,'email') ?: '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Role</div>
                <div class="detail-value"><?= ucfirst(s($staff,'role')) ?></div>
            </div>

            <div>
                <div class="detail-title">Address</div>
                <div class="detail-value"><?= nl2br(s($staff,'address')) ?: '—' ?></div>
            </div>

        </div>

    </div>
</div>
</main>

<?php include("../page/footer.php"); ?>
