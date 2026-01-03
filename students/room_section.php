<?php
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4">
            <div class="alert alert-danger">
                Database connection not available.
            </div>
         </div>');
}
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
   WHITE WRAPPER (MATCH student.php)
================================ */
.student-wrapper {
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
   STUDENT DIRECTORY BANNER
================================ */
.student-directory-banner {
    margin-top: -6rem;
    margin-bottom: -17rem;
    text-align: center;
    display: flex;
    justify-content: center;
}

.student-directory-banner img {
    max-width: 650px;
    width: 100%;
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

.student-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,.2);
}

.card-text {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: .5px;
    line-height: 1.4;
}

.student-card img {
    width: 140px;
    position: absolute;
    bottom: 15px;
    right: 10px;
}

/* ===============================
   DECORATIVE CORNER IMAGES
================================ */
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

<div class="student-wrapper">

    <!-- BANNER -->
    <div class="student-directory-banner">
        <img src="../room-banner.png" alt="Room Management">
    </div>

    <!-- BUILDING SECTION -->
    <div class="student-section">
        <div class="student-card-grid">

<?php
$sql = "
SELECT 
    b.building_id,
    b.building_name,
    COUNT(r.room_id) AS total_rooms
FROM building b
LEFT JOIN block bl 
    ON b.building_id = bl.building_id
LEFT JOIN room r 
    ON bl.block_id = r.block_id
WHERE b.status = 'Active'
GROUP BY b.building_id, b.building_name
ORDER BY b.building_name ASC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $buildingId   = (int)$row['building_id'];
        $buildingName = htmlspecialchars(strtoupper($row['building_name']));
        $roomCount    = (int)$row['total_rooms'];

        echo "
        <a href='block_by_building.php?building_id=$buildingId' class='student-card'>
            <span class='card-text'>
                $buildingName<br>
                <small style='font-size:0.85rem; font-weight:600; opacity:0.9;'>
                    $roomCount Rooms
                </small>
            </span>
            <img src='../building-logo.png' alt='$buildingName'>
        </a>
        ";
    }
} else {
    echo "<p class='text-center fw-bold'>No buildings available</p>";
}
?>

        </div>
    </div>

    <!-- DECOR -->
    <img src="../background-left.png" class="decor-left" alt="">
    <img src="../background-right.png" class="decor-right" alt="">

</div>
</main>

<?php include("../page/footer.php"); ?>
