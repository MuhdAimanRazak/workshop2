<?php
include("../config/config.php");

/* =========================
   GET STUDENT ID
========================= */
$student_id = $_GET['id'] ?? '';

if ($student_id === '') {
    header("Location: student.php");
    exit;
}

/* =========================
   FETCH STUDENT (NAME)
========================= */
$stmt = $conn->prepare(
    "SELECT full_name FROM student WHERE student_id = ?"
);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Student not found</h4></div>";
    exit;
}

$student = $result->fetch_assoc();

/* =========================
   CHANGE PASSWORD
========================= */
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 6) {
        $msg = "<div class='alert alert-danger'>
                    Password must be at least 6 characters.
                </div>";
    }
    elseif ($new_password !== $confirm_password) {
        $msg = "<div class='alert alert-danger'>
                    Passwords do not match.
                </div>";
    }
    else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare(
            "UPDATE student SET password = ? WHERE student_id = ?"
        );
        $update->bind_param("ss", $hashed, $student_id);
        $update->execute();

        header("Location: student_details.php?id=".$student_id);
        exit;
    }
}
?>

<?php include("../page/header.php"); ?>

<main>
<style>
/* ===== SAME UI AS STUDENT / STAFF ===== */
.edit-page {
    max-width: 700px;
    margin: 3rem auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    padding: 2rem;
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

<h3>Change Student Password</h3>
<p style="font-weight:600;">
    Student: <?= htmlspecialchars($student['full_name']) ?>
</p>

<?= $msg ?>

<form method="post">

    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" required>
    </div>

    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
    </div>

    <div class="btn-row">
        <a href="student_details.php?id=<?= $student_id ?>"
           class="btn btn-outline-secondary">
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

<?php include("../page/footer.php"); ?>
