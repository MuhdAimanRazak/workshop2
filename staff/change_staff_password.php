<?php
ob_start();
include("../page/header.php");

/* =========================
   CHANGE OWN PASSWORD ONLY
========================= */
$staff_id = $current_user_id;

if (empty($staff_id)) {
    header("Location: staff.php");
    exit;
}

/* =========================
   FETCH STAFF NAME
========================= */
$stmt = $conn->prepare("SELECT full_name FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Staff not found</h4></div>";
    exit;
}

$staff = $result->fetch_assoc();

/* =========================
   CHANGE PASSWORD
========================= */
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($new_password) < 6) {
        $msg = "<div class='alert alert-danger'>Password must be at least 6 characters.</div>";
    }
    elseif ($new_password !== $confirm_password) {
        $msg = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
    else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare(
            "UPDATE staff SET password = ? WHERE staff_id = ?"
        );
        $update->bind_param("ss", $hashed, $staff_id);
        $update->execute();

        header("Location: ../dash/index.php");
        exit;
    }
}
?>

<main>
<style>
.edit-page {
    max-width: 700px;
    margin: 3rem auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    padding: 2rem;
}
.form-group {
    position: relative;
}
.form-group label {
    font-weight:600;
    margin-bottom:6px;
    display:block;
}
.form-group input {
    width:100%;
    padding:.5rem .65rem;
    border-radius:8px;
    border:1px solid #e1e6f3;
    padding-right:45px;
}
.eye-btn {
    position:absolute;
    right:12px;
    top:36px;
    cursor:pointer;
}
.eye-btn img {
    width:20px;
    height:20px;
}
.btn-row {
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}
.btn-row .btn {
    border-radius:999px;
}
</style>

<div class="container">
<div class="edit-page">

<h3>Change Password</h3>
<p style="font-weight:600;">
    Staff: <?= htmlspecialchars($staff['full_name']) ?>
</p>

<?= $msg ?>

<form method="post" autocomplete="off" onsubmit="return confirmChange();">

    <div class="form-group">
        <label>New Password</label>
        <input type="password"
               id="new_password"
               name="new_password"
               autocomplete="new-password"
               required>
        <span class="eye-btn"
              onclick="togglePassword('new_password', this)">
            <img src="../assets/icons/eye.png" alt="Show password">
        </span>
    </div>

    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password"
               id="confirm_password"
               name="confirm_password"
               autocomplete="new-password"
               required>
        <span class="eye-btn"
              onclick="togglePassword('confirm_password', this)">
            <img src="../assets/icons/eye.png" alt="Show password">
        </span>
    </div>

    <div class="btn-row">
        <a href="../dash/index.php" class="btn btn-outline-secondary">
            Cancel
        </a>
        <button type="submit" class="btn btn-success">
            Update Password
        </button>
    </div>

</form>

</div>
</div>
</main>

<script>
function togglePassword(inputId, iconWrapper) {
    const input = document.getElementById(inputId);
    const img   = iconWrapper.querySelector("img");

    if (input.type === "password") {
        input.type = "text";
        img.src = "../assets/icons/eye-off.png";
        img.alt = "Hide password";
    } else {
        input.type = "password";
        img.src = "../assets/icons/eye.png";
        img.alt = "Show password";
    }
}

/* ===== DOUBLE CONFIRMATION ===== */
function confirmChange() {
    const p1 = document.getElementById('new_password').value;
    const p2 = document.getElementById('confirm_password').value;

    if (p1 !== p2) {
        alert("Passwords do not match.");
        return false;
    }

    return confirm(
        "⚠ Are you sure you want to change your password?\n\nYou will need to use the new password next time you log in."
    );
}
</script>

<?php include("../page/footer.php"); ?>
