<?php
include("../page/header.php");

$building_id = isset($_GET['building_id']) ? (int)$_GET['building_id'] : 0;

if ($building_id <= 0) {
    die('<div class="container mt-4">
            <div class="alert alert-warning">
                Invalid building selected.
            </div>
         </div>');
}
?>

<style>
/* ===============================
   PAGE WRAPPER
================================ */
.add-student-wrapper {
    padding: 4rem 3rem;
}

/* ===============================
   WHITE CONTAINER (BIGGER & CENTERED)
================================ */
.add-student-white {
    background: #ffffff;
    border-radius: 28px;
    padding: 5.5rem 5rem;
    min-height: 650px;

    max-width: 1200px;
    margin: 0 auto;

    box-shadow: 0 18px 45px rgba(0,0,0,.15);
}

/* ===============================
   TITLE
================================ */
.add-student-title {
    font-size: 1.6rem;
    font-weight: 800;
    margin-bottom: 2rem;
}

/* ===============================
   CHECKLIST
================================ */
.field-checklist {
    max-width: 420px;
    margin: 0 auto 3.5rem auto;
}

.field-checklist label {
    display: block;
    font-size: 1rem;
    margin-bottom: .6rem;
}

/* ===============================
   GRID (CENTER CONTENT)
================================ */
.add-student-grid {
    display: grid;
    grid-template-columns: repeat(2, 420px);
    gap: 4rem;
    justify-content: center;
}

/* ===============================
   BLUE ACTION TILE
================================ */
.add-student-card {
    background: #5f6dff;
    border-radius: 10px;
    padding: 4rem 3.8rem;
    min-height: 260px;

    color: #ffffff;
    text-decoration: none;
    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: space-between;

    transition: transform .25s ease, box-shadow .25s ease;
}

.add-student-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 22px 42px rgba(0,0,0,.25);
    color: #ffffff;
}

/* ===============================
   LEFT TEXT
================================ */
.add-student-text {
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: .7px;
    line-height: 1.7;
}

/* ===============================
   ICON
================================ */
.add-student-card img {
    width: 150px;
    height: auto;
    max-height: 150px;
    opacity: .95;
}
</style>

<main>
<div class="add-student-wrapper">

<div class="add-student-white">

    <!-- BACK -->
    <a href="student.php" class="back-link">← Back</a>

    <!-- TITLE -->
    <div class="add-student-title">
        Download Student List
    </div>

    <!-- FORM -->
    <form id="downloadForm" method="get">

        <input type="hidden" name="building_id" value="<?= $building_id ?>">

        <!-- CHECKLIST -->
        <div class="field-checklist">
            <strong>Select information to include:</strong><br><br>

            <label>
                <input type="checkbox" name="fields[]" value="name" checked>
                Full Name
            </label>

            <label>
                <input type="checkbox" name="fields[]" value="matric" checked>
                Matric Number
            </label>

            <label>
                <input type="checkbox" name="fields[]" value="ic">
                IC Number
            </label>

            <label>
                <input type="checkbox" name="fields[]" value="phone">
                Phone Number
            </label>
        </div>

        <!-- OPTIONS -->
        <div class="add-student-grid">

            <!-- DOWNLOAD CSV -->
            <div class="add-student-card"
                 onclick="submitDownload('download_students_csv.php')">
                <div class="add-student-text">
                    DOWNLOAD<br>
                    STUDENT LIST<br>
                    IN CSV FILE
                </div>
                <img src="../icon-csv.png" alt="Download CSV">
            </div>

            <!-- DOWNLOAD PDF -->
            <div class="add-student-card"
                 onclick="submitDownload('download_students_pdf.php')">
                <div class="add-student-text">
                    DOWNLOAD<br>
                    STUDENT LIST<br>
                    IN PDF FILE
                </div>
                <img src="../pdf-download.png" alt="Download PDF">
            </div>

        </div>

    </form>

</div>
</div>
</main>

<script>
function submitDownload(actionUrl) {
    const checked = document.querySelectorAll('input[name="fields[]"]:checked');
    if (checked.length === 0) {
        alert("Please select at least one information field.");
        return;
    }

    const form = document.getElementById("downloadForm");
    form.action = actionUrl;
    form.submit();
}
</script>

<?php
include("../page/footer.php");
?>
