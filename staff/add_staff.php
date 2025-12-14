<?php
include("../config/config.php");

/* =========================
   SAVE DATA (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =========================
       AUTO GENERATE STAFF ID
    ========================= */
    $prefix = "STF";

    $q = $conn->query("
        SELECT staff_id 
        FROM staff 
        WHERE staff_id LIKE '{$prefix}%'
        ORDER BY staff_id DESC 
        LIMIT 1
    ");

    if ($q && $q->num_rows > 0) {
        $lastId = $q->fetch_assoc()['staff_id'];
        $number = (int) substr($lastId, strlen($prefix));
        $number++;
    } else {
        $number = 1;
    }

    $staff_id = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    // contoh: STF00001

    /* =========================
       FORM DATA
    ========================= */
    $full_name = $_POST['full_name'];
    $phone_no  = $_POST['phone_no'];
    $staff_ic  = $_POST['staff_ic'];
    $email     = $_POST['email'];
    $address   = $_POST['address'];
    $role      = $_POST['role'];
    $password  = $_POST['password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO staff (
                staff_id,
                full_name,
                phone_no,
                staff_ic,
                email,
                address,
                role,
                password
            ) VALUES (?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssss",
        $staff_id,
        $full_name,
        $phone_no,
        $staff_ic,
        $email,
        $address,
        $role,
        $hashed
    );

    $stmt->execute();

    header("Location: staff_details.php?id=".$staff_id);
    exit;
}
?>

<?php include("../page/header.php"); ?>

<main>
<style>
/* ===== SAME CSS AS ADD STUDENT ===== */
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
.form-group select {
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

<h3>Add Staff</h3>

<form method="post">

<div class="edit-row">

    <!-- Staff ID (AUTO) -->
    <div class="form-group">
        <label>Staff ID</label>
        <input type="text" placeholder="Auto generated" disabled>
    </div>

    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" required>
    </div>

    <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone_no" required>
    </div>

    <div class="form-group">
        <label>IC Number</label>
        <input type="text" name="staff_ic" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Role</label>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="warden">Warden</option>
            <option value="staff">Staff</option>
        </select>
    </div>

    <div class="form-group full-row">
        <label>Address</label>
        <input type="text" name="address" required>
    </div>

    <div class="form-group full-row">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

</div>

<div class="btn-row">
    <a href="staff.php" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">Add Staff</button>
</div>

</form>

</div>
</div>
</main>

<?php include("../page/footer.php"); ?>
