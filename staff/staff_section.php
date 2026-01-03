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
   WHITE WRAPPER (FOLLOW STUDENT)
================================ */
.staff-wrapper {
    background: #ffffff;
    border-radius: 24px;
    padding: 3.5rem 3rem;
    margin-top: 0rem;
    margin-bottom: 2rem;
    padding-bottom: 12rem;
    box-shadow: 0 12px 30px rgba(0,0,0,.08);

    width: 100%;
    max-width: 100%;
    position: relative;
    overflow: hidden;
}

/* ===============================
   STAFF DIRECTORY BANNER
================================ */
.staff-directory-banner {
    margin-top: -6rem;
    margin-bottom: -17rem;
    text-align: center;
    display: flex;
    justify-content: center;
}

.staff-directory-banner img {
    max-width: 650px;
    width: 100%;
    height: auto;
}

/* ===============================
   STAFF SECTION
================================ */
.staff-section {
    margin-top: 10rem;
}

/* GRID */
.staff-card-grid {
    display: grid;
    grid-template-columns: repeat(3, 220px);
    gap: 2rem;
    justify-content: center;
}

/* CARD */
.staff-card {
    background: #3f4db8;
    border-radius: 10px;
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

.staff-card:hover {
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

/* ICON IMAGE */
.staff-card img {
    width: 120px;
    height: auto;
    position: absolute;
    bottom: 15px;
    right: 10px;
}

/* DECOR */
.decor-left {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 350px;
    opacity: 0.8;
    pointer-events: none;
}

.decor-right {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 350px;
    opacity: 0.8;
    pointer-events: none;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .staff-card-grid {
        grid-template-columns: repeat(2, 220px);
    }
}

@media (max-width: 520px) {
    .staff-card-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="container-fluid px-4">

    <!-- WHITE WRAPPER -->
    <div class="staff-wrapper">

        <!-- STAFF DIRECTORY BANNER -->
        <div class="staff-directory-banner">
            <img src="../staff-banner.png" alt="Staff Directory">
        </div>

        <!-- STAFF SECTION -->
        <div class="staff-section">

            <div class="staff-card-grid">

                <!-- VIEW ALL STAFF -->
                <a href="staff.php" class="staff-card">
                    <span class="card-text">
                        VIEW ALL<br>STAFF
                    </span>
                    <img src="../students.png" alt="All Staff">
                </a>

                <!-- ACTIVE STAFF -->
                <a href="staff.php?status=active" class="staff-card">
                    <span class="card-text">
                        ACTIVE<br>STAFF
                    </span>
                    <img src="../students.png" alt="Active Staff">
                </a>

                <!-- ARCHIVED STAFF -->
                <a href="staff.php?status=archived" class="staff-card">
                    <span class="card-text">
                        ARCHIVED<br>STAFF
                    </span>
                    <img src="../students.png" alt="Archived Staff">
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
