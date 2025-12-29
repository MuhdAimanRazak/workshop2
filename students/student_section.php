<?php
include("../page/header.php");
?>

<style>
/* ===============================
   WHITE WRAPPER (MATCH student.php)
================================ */
.student-wrapper {
    background: #ffffff;
    border-radius: 24px;
    padding: 3.5rem 3rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 12px 30px rgba(0,0,0,.08);
}

/* ===============================
   STUDENT DIRECTORY BANNER
================================ */
.student-directory-banner {
            margin-top: -10rem;
            margin-bottom: -33rem;
            text-align: center;
            overflow: hidden;
            display: flex;
            justify-content: center;
}

.student-directory-banner img {
           max-width: 1600px;
            width: 130%;
            margin-left: -250px;
            height: auto;
}

    

/* ===============================
   STUDENT SECTION
================================ */
.student-section {
    margin-top: 10rem;
}

/* GRID LAYOUT */
.student-card-grid {
    display: grid;
    grid-template-columns: repeat(3, 220px);
    gap: 2rem;
    justify-content: center;
}

/* CARD */
.student-card {
    background: #8894ff;
    border-radius: 18px;
    padding: 2rem 1.5rem;
    height: 170px;

    display: flex;
    flex-direction: column;
    justify-content: space-between;

    color: #fff;
    text-decoration: none;

    box-shadow: 0 12px 25px rgba(0,0,0,.12);
    transition: all .25s ease;
    position: relative;
}

/* HOVER EFFECT */
.student-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,.2);
}

/* TEXT */
.card-text {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: .5px;
    line-height: 1.4;
}

/* IMAGE (BIGGER ICON) */
.student-card img {
    width: 140px;
    height: auto;
    opacity: 1;
    position: absolute;
    bottom: 15px;
    right: 10px;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .student-card-grid {
        grid-template-columns: repeat(2, 220px);
    }
}

@media (max-width: 520px) {
    .student-card-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="container-fluid px-4">

    <!-- WHITE WRAPPER -->
    <div class="student-wrapper">

        <!-- STUDENT DIRECTORY BANNER -->
        <div class="student-directory-banner">
            <img src="../studentsearch.png" alt="Student Directory">
        </div>

        <!-- STUDENT SECTION -->
        <div class="student-section">

            <div class="student-card-grid">

                <!-- SATRIA -->
                <a href="student_by_building.php?building=satria" class="student-card">
                    <span class="card-text">SATRIA</span>
                    <img src="../building-logo.png" alt="Satria">
                </a>

                <!-- LESTARI -->
                <a href="student_by_building.php?building=lestari" class="student-card">
                    <span class="card-text">LESTARI</span>
                    <img src="../building-logo.png" alt="Lestari">
                </a>

                <!-- AL-JAZARI -->
                <a href="student_by_building.php?building=aljazari" class="student-card">
                    <span class="card-text">AL-JAZARI</span>
                    <img src="../building-logo.png" alt="Al-Jazari">
                </a>

                <!-- VIEW ALL -->
                <a href="student.php" class="student-card">
                    <span class="card-text">
                        VIEW ALL<br>STUDENTS
                    </span>
                    <img src="../students.png" alt="All Students">
                </a>

                <!-- ARCHIVED -->
                <a href="archived_students.php" class="student-card">
                    <span class="card-text">
                        ARCHIVED<br>STUDENTS
                    </span>
                    <img src="../archive.png" alt="Archived Students">
                </a>

            </div>

        </div>

    </div>

</main>

<?php
include("../page/footer.php");
?>
