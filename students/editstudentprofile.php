<?php
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id     = $_POST['student_id'];
    $full_name      = $_POST['full_name'];
    $phone_no       = $_POST['phone_no'];
    $student_ic     = $_POST['student_ic'];
    $parent_contact = $_POST['parent_contact'];
    $course         = $_POST['course'];
    $email          = $_POST['email'];
    $address        = $_POST['address'];

    // AUTO FACULTY FROM COURSE
    if (strpos($course, 'Computer') !== false || strpos($course, 'Information Technology') !== false) {
        $faculty = "FTMK";
    } elseif (strpos($course, 'Electrical') !== false || strpos($course, 'Electronics') !== false || strpos($course, 'Mechatronics') !== false) {
        $faculty = "FKE";
    } elseif (strpos($course, 'Mechanical') !== false || strpos($course, 'Automotive') !== false || strpos($course, 'Industrial') !== false) {
        $faculty = "FKM";
    } elseif (strpos($course, 'Manufacturing') !== false || strpos($course, 'Product Design') !== false || strpos($course, 'Materials') !== false) {
        $faculty = "FKP";
    } elseif (strpos($course, 'Technology') !== false) {
        $faculty = "FTKMP";
    } else {
        $faculty = NULL;
    }

    $sql = "UPDATE student SET
                full_name = ?,
                phone_no = ?,
                student_ic = ?,
                parent_contact = ?,
                course = ?,
                faculty = ?,
                email = ?,
                address = ?
            WHERE student_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssss",
        $full_name,
        $phone_no,
        $student_ic,
        $parent_contact,
        $course,
        $faculty,
        $email,
        $address,
        $student_id
    );

    $stmt->execute();

    header("Location: student_details.php?id=".$student_id);
    exit;
}

/* =========================
   GET DATA (DISPLAY)
========================= */
$student_id = $_GET['id'] ?? '';

$sql = "SELECT * FROM student WHERE student_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

function s($k){ global $student; return htmlspecialchars($student[$k] ?? ''); }
?>

<?php include("../page/header.php"); ?>

<main>
<style>
/* ===== UI AS IS – TAK SENTUH ===== */
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

<h3>Edit Student Profile</h3>

<form method="post">

<div class="edit-row">

<div class="form-group full-row">
<label>Full Name</label>
<input type="text" name="full_name" value="<?= s('full_name') ?>">
</div>

<div class="form-group">
<label>Matric Number</label>
<input type="text" name="student_id" value="<?= s('student_id') ?>">
</div>

<input type="hidden" name="faculty" id="faculty" value="<?= s('faculty') ?>">



<div class="form-group">
<label>Phone Number</label>
<input type="text" name="phone_no" value="<?= s('phone_no') ?>">
</div>

<div class="form-group">
<label>IC Number</label>
<input type="text" name="student_ic" value="<?= s('student_ic') ?>">
</div>

<div class="form-group">
<label>Guardian's Number</label>
<input type="text" name="parent_contact" value="<?= s('parent_contact') ?>">
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?= s('email') ?>">
</div>

<div>
    <div class="detail-title">Programme</div>
    <div class="detail-value">
        <select name="course" style="width:100%; padding:.4rem;">
            <optgroup label="FTMK – Fakulti Teknologi Maklumat & Komunikasi">
                <option <?= ($student['course']=="Bachelor of Computer Science (Artificial Intelligence)")?"selected":"" ?>>
                    Bachelor of Computer Science (Artificial Intelligence)
                </option>
                <option <?= ($student['course']=="Bachelor of Computer Science (Data Engineering)")?"selected":"" ?>>
                    Bachelor of Computer Science (Data Engineering)
                </option>
                <option <?= ($student['course']=="Bachelor of Computer Science (Software Development)")?"selected":"" ?>>
                    Bachelor of Computer Science (Software Development)
                </option>
                <option <?= ($student['course']=="Bachelor of Computer Science (Cybersecurity)")?"selected":"" ?>>
                    Bachelor of Computer Science (Cybersecurity)
                </option>
                <option <?= ($student['course']=="Bachelor of Information Technology (Information Security)")?"selected":"" ?>>
                    Bachelor of Information Technology (Information Security)
                </option>
                <option <?= ($student['course']=="Bachelor of Information Technology (Software Engineering)")?"selected":"" ?>>
                    Bachelor of Information Technology (Software Engineering)
                </option>
                <option <?= ($student['course']=="Diploma in Computer Science")?"selected":"" ?>>
                    Diploma in Computer Science
                </option>
                <option <?= ($student['course']=="Diploma in Information Technology")?"selected":"" ?>>
                    Diploma in Information Technology
                </option>
            </optgroup>

            <optgroup label="FKE – Fakulti Kejuruteraan Elektrik">
                <option <?= ($student['course']=="Bachelor of Electrical Engineering")?"selected":"" ?>>
                    Bachelor of Electrical Engineering
                </option>
                <option <?= ($student['course']=="Bachelor of Electronics Engineering")?"selected":"" ?>>
                    Bachelor of Electronics Engineering
                </option>
                <option <?= ($student['course']=="Bachelor of Mechatronics Engineering")?"selected":"" ?>>
                    Bachelor of Mechatronics Engineering
                </option>
                <option <?= ($student['course']=="Diploma in Electrical Engineering")?"selected":"" ?>>
                    Diploma in Electrical Engineering
                </option>
                <option <?= ($student['course']=="Diploma in Electronics Engineering")?"selected":"" ?>>
                    Diploma in Electronics Engineering
                </option>
            </optgroup>

            <optgroup label="FKM – Fakulti Kejuruteraan Mekanikal">
                <option <?= ($student['course']=="Bachelor of Mechanical Engineering")?"selected":"" ?>>
                    Bachelor of Mechanical Engineering
                </option>
                <option <?= ($student['course']=="Bachelor of Manufacturing Engineering")?"selected":"" ?>>
                    Bachelor of Manufacturing Engineering
                </option>
                <option <?= ($student['course']=="Bachelor of Industrial Engineering")?"selected":"" ?>>
                    Bachelor of Industrial Engineering
                </option>
                <option <?= ($student['course']=="Bachelor of Automotive Engineering")?"selected":"" ?>>
                    Bachelor of Automotive Engineering
                </option>
                <option <?= ($student['course']=="Diploma in Mechanical Engineering")?"selected":"" ?>>
                    Diploma in Mechanical Engineering
                </option>
                <option <?= ($student['course']=="Diploma in Industrial Engineering")?"selected":"" ?>>
                    Diploma in Industrial Engineering
                </option>
            </optgroup>
        </select>
    </div>
</div>


<div class="form-group full-row">
<label>Address</label>
<input type="text" name="address" value="<?= s('address') ?>">
</div>

</div>

<div class="btn-row">
<a href="student_details.php?id=<?= s('student_id') ?>" 
   class="btn btn-outline-secondary">
   Cancel
</a>

<button type="submit" class="btn btn-success">Save</button>
</div>

</form>
</div>
</div>

<script>
const programmeToFaculty = {
    "Bachelor of Computer Science (Artificial Intelligence)": "FTMK",
    "Bachelor of Computer Science (Data Engineering)": "FTMK",
    "Bachelor of Computer Science (Software Development)": "FTMK",
    "Bachelor of Computer Science (Cybersecurity)": "FTMK",
    "Bachelor of Information Technology (Information Security)": "FTMK",
    "Bachelor of Information Technology (Software Engineering)": "FTMK",
    "Diploma in Computer Science": "FTMK",
    "Diploma in Information Technology": "FTMK",

    "Bachelor of Electrical Engineering": "FKE",
    "Bachelor of Electronics Engineering": "FKE",
    "Bachelor of Mechatronics Engineering": "FKE",
    "Diploma in Electrical Engineering": "FKE",
    "Diploma in Electronics Engineering": "FKE",

    "Bachelor of Mechanical Engineering": "FKM",
    "Bachelor of Manufacturing Engineering": "FKM",
    "Bachelor of Industrial Engineering": "FKM",
    "Bachelor of Automotive Engineering": "FKM",
    "Diploma in Mechanical Engineering": "FKM",
    "Diploma in Industrial Engineering": "FKM",

    "Bachelor of Manufacturing Engineering": "FKP",
    "Bachelor of Product Design Engineering": "FKP",
    "Bachelor of Materials Engineering": "FKP",
    "Diploma in Manufacturing Engineering": "FKP",

    "Bachelor of Computer Engineering": "FKEKK",
    "Bachelor of Electronics Engineering (Computer Engineering)": "FKEKK",
    "Bachelor of Telecommunication Engineering": "FKEKK",
    "Diploma in Computer Engineering": "FKEKK",
    "Diploma in Electronics Engineering": "FKEKK",

    "Bachelor of Technology in Automotive Technology": "FTKMP",
    "Bachelor of Technology in Manufacturing Systems": "FTKMP",
    "Bachelor of Technology in Welding": "FTKMP",
    "Bachelor of Technology in Industrial Automation": "FTKMP",
    "Bachelor of Technology in Robotics & Automation": "FTKMP",
    "Diploma in Technology (Automotive / Electronic / Manufacturing)": "FTKMP",

    "Bachelor of Technology Management": "FPTT",
    "Bachelor of Technology Entrepreneurship": "FPTT",
    "Diploma in Technology Management": "FPTT"
};

const programmeSelect = document.querySelector('select[name="course"]');
const facultyInput = document.getElementById('faculty');

if (programmeSelect) {
    programmeSelect.addEventListener('change', function () {
        const selectedProgramme = this.value;
        facultyInput.value = programmeToFaculty[selectedProgramme] || '';
    });
}
</script>

</main>

<?php include("../page/footer.php"); ?>
