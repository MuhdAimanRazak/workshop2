<?php
// add_student_choice.php
include("../page/header.php");
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

    max-width: 1200px;     /* MAKE IT BIG */
    margin: 0 auto;        /* CENTER IT */

    box-shadow: 0 18px 45px rgba(0,0,0,.15);
}

/* ===============================
   TITLE
================================ */
.add-student-title {
    font-size: 1.6rem;
    font-weight: 800;
    margin-bottom: 3.8rem;
}

/* ===============================
   GRID (CENTER CONTENT)
================================ */
.add-student-grid {
    display: grid;
    grid-template-columns: repeat(2, 420px); /* FIXED CARD WIDTH */
    gap: 4rem;
    justify-content: center;  /* CENTER THE BUTTONS */
}

/* ===============================
   BLUE ACTION TILE
================================ */
.add-student-card {
    background: #7f8cff;
    border-radius: 10px;
    padding: 4rem 3.8rem;
    min-height: 260px;

    color: #ffffff;
    text-decoration: none;

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
   ICON (BIG & NOT KEMEK)
================================ */
.add-student-card img {
    width: 150px;
    height: auto;
    max-height: 150px;
    opacity: .95;
}

/* ===============================
   RESPONSIVE
================================ */
@media (max-width: 1100px) {
    .add-student-grid {
        grid-template-columns: 1fr;
    }

    .add-student-white {
        padding: 4rem 3rem;
        min-height: auto;
    }

    .add-student-card {
        width: 100%;
        min-height: 230px;
        padding: 3rem;
    }

    .add-student-card img {
        width: 120px;
    }
}


</style>

<main>
    <div class="add-student-wrapper">

        <div class="add-student-white">
<a href="student.php" class="back-link">← Back</a>
            <div class="add-student-title">
                Add Student
            </div>

            <div class="add-student-grid">

                <!-- ADD STUDENT USING CSV -->
                <a href="students_import_form.php" class="add-student-card">
                    <div class="add-student-text">
                        ADD STUDENT<br>
                        USING CSV
                    </div>
                    <img src="../icon-csv.png" alt="Add student using CSV">
                </a>

                <!-- ADD STUDENT MANUALLY -->
                <a href="add_student_manually.php" class="add-student-card">
                    <div class="add-student-text">
                        ADD STUDENT<br>
                        MANUALLY
                    </div>
                    <img src="../icon-manual.png" alt="Add student manually">
                </a>

            </div>

        </div>

    </div>
</main>

<?php
include("../page/footer.php");
?>
