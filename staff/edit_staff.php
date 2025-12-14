<?php
include("../config/config.php");

/* =========================
   GET STAFF ID
========================= */
$staff_id = $_GET['id'] ?? '';

if ($staff_id === '') {
    header("Location: staff.php");
    exit;
}

/* =========================
   FETCH STAFF
========================= */
$sql = "SELECT staff_id, full_name, email, phone_no, staff_ic, address, role
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

/* =========================
   UPDATE STAFF
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = $_POST['full_name'];
    $phone_no  = $_POST['phone_no'];
    $staff_ic  = $_POST['staff_ic'];
    $email     = $_POST['email'];
    $address   = $_POST['address'];
    $role      = $_POST['role'];

    $update = $conn->prepare(
        "UPDATE staff
         SET full_name=?, phone_no=?, staff_ic=?, email=?, address=?, role=?
         WHERE staff_id=?"
    );

    $update->bind_param(
        "sssssss",
        $full_name,
        $phone_no,
        $staff_ic,
        $email,
        $address,
        $role,
        $staff_id
    );

    $update->execute();

    header("Location: staff_details.php?id=".$staff_id);
    exit;
}
?>

<?php include("../page/header.php"); ?>

<main>
<style>
/* ===== SAME UI AS STUDENT ===== */
.edit-page {
    max-width: 1100px;
    margin: 3rem auto;
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
.full-row { grid-column:1/-1; }
.btn-row {
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}
.btn-row .btn { border-radius:999px; }
</style>

<div class="container">
<div class="edit-page">

<h3>Edit Staff</h3>

<form method="post">

<div class="edit-row">

    <div class="form-group">
        <label>Staff ID</label>
        <input type="text" value="<?= htmlspecialchars($staff['staff_id']) ?>" disabled>
    </div>

    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name"
               value="<?= htmlspecialchars($staff['full_name']) ?>" required>
    </div>

    <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone_no"
               value="<?= htmlspecialchars($staff['phone_no']) ?>" required>
    </div>

    <div class="form-group">
        <label>IC Number</label>
        <input type="text" name="staff_ic"
               value="<?= htmlspecialchars($staff['staff_ic']) ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($staff['email']) ?>" required>
    </div>

    <div class="form-group">
        <label>Role</label>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin"  <?= $staff['role']=='admin'?'selected':'' ?>>Admin</option>
            <option value="warden" <?= $staff['role']=='warden'?'selected':'' ?>>Warden</option>
            <option value="staff"  <?= $staff['role']=='staff'?'selected':'' ?>>Staff</option>
        </select>
    </div>

    <div class="form-group full-row">
        <label>Address</label>
        <textarea name="address" rows="3" required><?= htmlspecialchars($staff['address']) ?></textarea>
    </div>

</div>

<div class="btn-row">
    <a href="staff_details.php?id=<?= $staff_id ?>" class="btn btn-outline-secondary">
        Cancel
    </a>
    <button type="submit" class="btn btn-success">
        Save Changes
    </button>
</div>

</form>

</div>
</div>
</main>

<?php include("../page/footer.php"); ?>
