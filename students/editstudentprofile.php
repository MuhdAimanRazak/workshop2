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

    
$bedKey = $_POST['bed_key'] ?? '';
$status_payment = $_POST['status_payment'] ?? 'Pending';


if ($bedKey) {
    list($room_id, $bed_no) = explode('|', $bedKey);

    /* =========================
       CHECK EXISTING BOOKING
    ========================= */
    $chk = $conn->prepare("
        SELECT room_id, bed_no
        FROM booking
        WHERE student_id = ?
        LIMIT 1
    ");
    $chk->bind_param("s", $student_id);
    $chk->execute();
    $old = $chk->get_result()->fetch_assoc();

    if ($old) {
        // 1️⃣ Free OLD bed
        $free = $conn->prepare("
            UPDATE room
            SET status_bed = 'available'
            WHERE room_id = ? AND bed_no = ?
        ");
        $free->bind_param("ii", $old['room_id'], $old['bed_no']);
        $free->execute();

        // 2️⃣ Update booking
        $upd = $conn->prepare("
        UPDATE booking
        SET room_id = ?, bed_no = ?, status_payment = ?
         WHERE student_id = ?
        ");
        $upd->bind_param("iiss", $room_id, $bed_no, $status_payment, $student_id);

        $upd->execute();

    } else {
        // 3️⃣ Insert new booking (admin assign)
$ins = $conn->prepare("
    INSERT INTO booking (student_id, room_id, bed_no, status_payment)
    VALUES (?, ?, ?, ?)
");
$ins->bind_param("siis", $student_id, $room_id, $bed_no, $status_payment);

        $ins->execute();
    }

    // 4️⃣ Occupy NEW bed
    $occ = $conn->prepare("
        UPDATE room
        SET status_bed = 'occupied'
        WHERE room_id = ? AND bed_no = ?
    ");
    $occ->bind_param("ii", $room_id, $bed_no);
    $occ->execute();
}

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
html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}

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
    color: #333;
}

.form-group input, .form-group select {
    width:100%;
    padding:.5rem .65rem;
    border-radius:8px;
    border:1px solid #e1e6f3;
    font-size: 0.95rem;
}

.form-group input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
    color: #6c757d;
}

.full-row{
    grid-column:1/-1;
}

.btn-row{
    display:flex;
    justify-content:flex-end;
    gap:.6rem;
    margin-top:1rem;
}

.btn-row .btn{
    border-radius:999px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
}

.hostel-section {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: #f8faff;
    border-radius: 12px;
    border: 1px solid #e2e8ff;
}

.hostel-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #2a2a8c;
    margin-bottom: 1rem;
}

.hostel-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem 1.25rem;
}

.hostel-grid .form-group {
    margin: 0;
}

.hostel-grid select {
    background-color: #fff;
}

#house-group {
    display: none;
}

@media (max-width: 768px) {
    .hostel-grid {
        grid-template-columns: 1fr;
    }
    .edit-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container">
<div class="edit-page">

<h3>Edit Student Profile</h3>

<form method="post" id="editForm">

<div class="edit-row">

<div class="form-group full-row">
<label>Full Name</label>
<input type="text" name="full_name" value="<?= s('full_name') ?>" required>
</div>

<div class="form-group">
<label>Matric Number</label>
<input type="text" value="<?= s('student_id') ?>" disabled readonly>
<input type="hidden" name="student_id" value="<?= s('student_id') ?>">
</div>

<div class="form-group">
<label>Phone Number</label>
<input type="text" name="phone_no" value="<?= s('phone_no') ?>" required>
</div>

<div class="form-group">
<label>IC Number</label>
<input type="text" value="<?= s('student_ic') ?>" disabled readonly>
<input type="hidden" name="student_ic" value="<?= s('student_ic') ?>">
</div>

<div class="form-group">
<label>Guardian's Number</label>
<input type="text" name="parent_contact" value="<?= s('parent_contact') ?>" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?= s('email') ?>" required>
</div>

<div class="form-group">
<label>Programme</label>
<select name="course" required>
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

<input type="hidden" name="faculty" id="faculty" value="<?= s('faculty') ?>">

<div class="form-group full-row">
<label>Address</label>
<input type="text" name="address" value="<?= s('address') ?>" required>
</div>

</div>

<div class="hostel-section">
    <div class="hostel-title">Hostel Assignment</div>

    <div class="hostel-grid">

        <div class="form-group">
            <label>Building</label>
            <select id="building" class="form-control">
                <option value="">-- Select Building --</option>
                <?php
                $bq = $conn->query("SELECT building_id, building_name FROM building");
                while ($b = $bq->fetch_assoc()):
                ?>
                    <option value="<?= $b['building_id'] ?>">
                        <?= $b['building_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Block</label>
            <select id="block" class="form-control" disabled>
                <option>-- Select Building First --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Level</label>
            <select id="level" class="form-control" disabled>
                <option>-- Select Block First --</option>
            </select>
        </div>

        <div class="form-group" id="house-group">
            <label>House</label>
            <select id="house" class="form-control" disabled>
                <option>-- Select Level First --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Room</label>
            <select id="room" class="form-control" disabled>
                <option>-- Select Level First --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Bed</label>
            <select name="bed_key" id="bed" class="form-control" disabled>
                <option>-- Select Room First --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Payment Status</label>
            <select name="status_payment" class="form-control">
                <option value="Pending">Pending</option>
                <option value="Paid">Paid</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>

    </div>
</div>

<div class="btn-row">
<a href="student_details.php?id=<?= s('student_id') ?>" 
   class="btn btn-outline-secondary">
   Cancel
</a>

<button type="submit" class="btn btn-success">Save Changes</button>
</div>

</form>
</div>
</div>

<script>
// Form submission confirmation
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (confirm('Are you sure you want to save these changes to the student profile?')) {
        this.submit();
    }
});
</script>

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

<script>
const building = document.getElementById('building');
const block    = document.getElementById('block');
const level    = document.getElementById('level');
const house    = document.getElementById('house');
const room     = document.getElementById('room');
const bed      = document.getElementById('bed');

const houseGroup = document.getElementById('house-group');

function resetSelect(el, text) {
    el.innerHTML = `<option>${text}</option>`;
    el.disabled = true;
}

building.onchange = () => {
    resetSelect(block, '-- Select Building First --');
    resetSelect(level, '-- Select Block First --');
    resetSelect(room, '-- Select Level First --');
    resetSelect(bed, '-- Select Room First --');

    houseGroup.style.display = 'none';
    resetSelect(house, '-- Select Level First --');

    fetch(`get_blocks.php?building_id=${building.value}`)
        .then(res => res.text())
        .then(html => {
            block.innerHTML = '<option>-- Select Block --</option>' + html;
            block.disabled = false;
        });
};

block.onchange = () => {
    resetSelect(level, '-- Select Block First --');
    resetSelect(room, '-- Select Level First --');
    resetSelect(bed, '-- Select Room First --');

    houseGroup.style.display = 'none';
    resetSelect(house, '-- Select Level First --');

    fetch(`get_levels.php?block_id=${block.value}`)
        .then(res => res.text())
        .then(html => {
            level.innerHTML = '<option>-- Select Level --</option>' + html;
            level.disabled = false;
        });
};

level.onchange = () => {
    resetSelect(room, '-- Select Level First --');
    resetSelect(bed, '-- Select Room First --');

    fetch(`get_houses.php?block_id=${block.value}&level=${level.value}`)
        .then(res => res.text())
        .then(html => {
            if (html.trim() !== '') {
                houseGroup.style.display = 'block';
                house.innerHTML = '<option>-- Select House --</option>' + html;
                house.disabled = false;
            } else {
                houseGroup.style.display = 'none';

                fetch(`get_rooms.php?block_id=${block.value}&level=${level.value}`)
                    .then(r => r.text())
                    .then(h => {
                        room.innerHTML = '<option>-- Select Room --</option>' + h;
                        room.disabled = false;
                    });
            }
        });
};

house.onchange = () => {
    resetSelect(room, '-- Select House First --');
    resetSelect(bed, '-- Select Room First --');

    fetch(`get_rooms.php?block_id=${block.value}&level=${level.value}&house=${house.value}`)
        .then(res => res.text())
        .then(html => {
            room.innerHTML = '<option>-- Select Room --</option>' + html;
            room.disabled = false;
        });
};

room.onchange = () => {
    resetSelect(bed, '-- Select Room First --');

    fetch(`get_beds.php?room_id=${room.value}`)
        .then(res => res.text())
        .then(html => {
            bed.innerHTML = '<option>-- Select Bed --</option>' + html;
            bed.disabled = false;
        });
};
</script>

</main>

<?php include("../page/footer.php"); ?>