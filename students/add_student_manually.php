<?php
include("../config/config.php");

/* =========================
   SAVE DATA (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =========================
    // AUTO GENERATE STUDENT ID
    // =========================
    $prefix = "B0323";

    $q = $conn->query("
        SELECT student_id 
        FROM student 
        WHERE student_id LIKE '{$prefix}%'
        ORDER BY student_id DESC 
        LIMIT 1
    ");

    if ($q && $q->num_rows > 0) {
        $lastId = $q->fetch_assoc()['student_id'];
        $number = (int) substr($lastId, 5);
        $number++;
    } else {
        $number = 1;
    }

    $student_id = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    // contoh: B032300001

    // =========================
    // FORM DATA
    // =========================
    $full_name      = $_POST['full_name'];
    $phone_no       = $_POST['phone_no'];
    $student_ic     = $_POST['student_ic'];
    $parent_contact = $_POST['parent_contact'];
    $course         = $_POST['course'];
    $email          = $_POST['email'];
    $address        = $_POST['address'];

    // =========================
    // FACULTY MAPPING (FULL)
    // =========================
    $programmeToFaculty = [
        "Bachelor of Computer Science (Artificial Intelligence)" => "FTMK",
        "Bachelor of Computer Science (Data Engineering)" => "FTMK",
        "Bachelor of Computer Science (Software Development)" => "FTMK",
        "Bachelor of Computer Science (Cybersecurity)" => "FTMK",
        "Bachelor of Information Technology (Information Security)" => "FTMK",
        "Bachelor of Information Technology (Software Engineering)" => "FTMK",
        "Diploma in Computer Science" => "FTMK",
        "Diploma in Information Technology" => "FTMK",

        "Bachelor of Electrical Engineering" => "FKE",
        "Bachelor of Electronics Engineering" => "FKE",
        "Bachelor of Mechatronics Engineering" => "FKE",
        "Diploma in Electrical Engineering" => "FKE",
        "Diploma in Electronics Engineering" => "FKE",

        "Bachelor of Mechanical Engineering" => "FKM",
        "Bachelor of Manufacturing Engineering" => "FKM",
        "Bachelor of Industrial Engineering" => "FKM",
        "Bachelor of Automotive Engineering" => "FKM",
        "Diploma in Mechanical Engineering" => "FKM",
        "Diploma in Industrial Engineering" => "FKM"
    ];

    $faculty = $programmeToFaculty[$course] ?? "-";

    // =========================
    // INSERT
    // =========================
    $sql = "INSERT INTO student (
                student_id,
                full_name,
                phone_no,
                student_ic,
                parent_contact,
                course,
                faculty,
                email,
                address
            ) VALUES (?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssss",
        $student_id,
        $full_name,
        $phone_no,
        $student_ic,
        $parent_contact,
        $course,
        $faculty,
        $email,
        $address
    );

    if (!$stmt->execute()) {
        die("<b>DB INSERT ERROR:</b> " . $stmt->error);
    }

    header("Location: student_details.php?id=".$student_id);
    exit;
}
?>

<?php include("../page/header.php"); ?>

<main>
<style>
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
.form-group input, .form-group select {
    width:100%;
    padding:.5rem .65rem;
    border-radius:8px;
    border:1px solid #e1e6f3;
}
.full-row{grid-column:1/-1;}
.btn-row{
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}
.btn-row .btn{border-radius:999px;}
</style>

<div class="container">
<div class="edit-page">

<h3>Add Student</h3>

<form method="post">

<div class="edit-row">

<div class="form-group">
    <label>Matric Number</label>
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
<input type="text" name="student_ic" required>
</div>

<div class="form-group">
<label>Guardian's Number</label>
<input type="text" name="parent_contact" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="form-group full-row">
<label>Programme</label>
<select name="course" required>
    <option value="">-- Select Programme --</option>

    <optgroup label="FTMK">
        <option>Bachelor of Computer Science (Artificial Intelligence)</option>
        <option>Bachelor of Computer Science (Data Engineering)</option>
        <option>Bachelor of Computer Science (Software Development)</option>
        <option>Bachelor of Computer Science (Cybersecurity)</option>
        <option>Bachelor of Information Technology (Information Security)</option>
        <option>Bachelor of Information Technology (Software Engineering)</option>
        <option>Diploma in Computer Science</option>
        <option>Diploma in Information Technology</option>
    </optgroup>

    <optgroup label="FKE">
        <option>Bachelor of Electrical Engineering</option>
        <option>Bachelor of Electronics Engineering</option>
        <option>Bachelor of Mechatronics Engineering</option>
        <option>Diploma in Electrical Engineering</option>
        <option>Diploma in Electronics Engineering</option>
    </optgroup>

    <optgroup label="FKM">
        <option>Bachelor of Mechanical Engineering</option>
        <option>Bachelor of Manufacturing Engineering</option>
        <option>Bachelor of Industrial Engineering</option>
        <option>Bachelor of Automotive Engineering</option>
        <option>Diploma in Mechanical Engineering</option>
        <option>Diploma in Industrial Engineering</option>
    </optgroup>
</select>
</div>

<div class="form-group full-row">
<label>Address</label>
<input type="text" name="address" required>
</div>

</div>

<div class="btn-row">
<a href="add_student_choice.php" class="btn btn-outline-secondary">Cancel</a>
<button type="submit" class="btn btn-success">Add Student</button>
</div>

</form>

</div>
</div>
</main>

<?php include("../page/footer.php"); ?>
