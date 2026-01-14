<?php
include("../page/header.php");
?>

<style>
/* ===============================
   FORCE FULL WHITE BACKGROUND
================================ */
html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}

/* ===============================
   WHITE WRAPPER 
================================ */
.student-wrapper {
    background: #ffffff;
    border-radius: 24px;
    padding: 2rem 3rem;
    margin: 1rem auto 2rem auto;
    box-shadow: 0 12px 30px rgba(0,0,0,.08);
    width: 100%;
    max-width: 1200px;
    position: relative;
    overflow: hidden;
}

/* ===============================
   STUDENT DIRECTORY BANNER
================================ */
.student-directory-banner {
    text-align: center;
    margin-bottom: 2rem;
}

.student-directory-banner img {
    max-width: 650px;
    width: 100%;
    height: auto;
}

/* ===============================
   STUDENT SECTION (Cards)
================================ */
.student-section {
    display: flex;
    justify-content: center;
}

.student-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    justify-items: center;
}

/* ===============================
   CARD
================================ */
.student-card {
    background: #3f4db8;
    border-radius: 10px;
    padding: 2rem 1.5rem;
    height: 170px;
    width: 100%;
    max-width: 250px;

    display: flex;
    flex-direction: column;
    justify-content: space-between;

    color: #fff;
    text-decoration: none;

    box-shadow: 0 12px 25px rgba(0,0,0,.12);
    transition: all .25s ease;
    position: relative;
}

.student-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,.2);
}

/* CARD TEXT */
.card-text {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: .5px;
    line-height: 1.4;
}

/* CARD IMAGE */
.student-card img {
    width: 100px;
    height: auto;
    position: absolute;
    bottom: 15px;
    right: 16px;
}

/* ===============================
   DECORATIVE CORNER IMAGES
================================ */
.decor-left {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 250px;
    opacity: 0.5;
    pointer-events: none;
}

.decor-right {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 250px;
    opacity: 0.5;
    pointer-events: none;
}

/* RESPONSIVE ADJUSTMENTS */
@media (max-width: 900px) {
    .student-card-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    .decor-left, .decor-right {
        width: 150px;
        opacity: 0.3;
    }
}

@media (max-width: 520px) {
    .student-card-grid {
        grid-template-columns: 1fr;
    }
    .decor-left, .decor-right {
        width: 120px;
        opacity: 0.2;
    }
}
</style>

<main class="container-fluid px-0">

    <!-- WHITE WRAPPER -->
    <div class="student-wrapper">

        <!-- STUDENT DIRECTORY BANNER -->
        <div class="student-directory-banner">
            <img src="../facility.png" alt="building-banner">
        </div>

        <!-- STUDENT SECTION -->
        <div class="student-section">
            <div class="student-card-grid">

                <!-- View All Building -->
                <a href="building_all.php" class="student-card">
                    <span class="card-text">View All Building</span>
                    <img src="../building-logo.png" alt="Building">
                </a>

                <!-- View Active Building -->
                <a href="building_Active.php" class="student-card">
                    <span class="card-text">View Active Building</span>
                    <img src="../building-logo.png" alt="Building">
                </a>

                <!-- View Inactive Building -->
                <a href="building_Inactive.php" class="student-card">
                    <span class="card-text">View Inactive Building</span>
                    <img src="../building-logo.png" alt="Building">
                </a>

            </div>
        </div>

        <!-- Decorative Images -->
        <img src="../background-left.png" class="decor-left" alt="">
        <img src="../background-right.png" class="decor-right" alt="">

    </div>

</main>

<?php
include("../page/footer.php");
?>
