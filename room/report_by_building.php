<?php
include("../page/header.php");
include("../config/config.php");

if (!isset($conn) || !$conn) {
    die('<div class="container mt-4">
            <div class="alert alert-danger">Database connection not available.</div>
         </div>');
}

?>

<style>


html, body {
    background: #ffffff !important;
}

/* WRAPPER */
.student-wrapper {
    background: #ffffff;
    border-radius: 24px;
    padding: 3.5rem 3rem;
    padding-bottom: 12rem;
    box-shadow: 0 12px 30px rgba(0,0,0,.08);
    position: relative;
}

/* BANNER */
.student-directory-banner {
    margin-top: -6rem;
    margin-bottom: -17rem;
    display: flex;
    justify-content: center;
}

.student-directory-banner img {
    max-width: 650px;
}

/* GRID */
.student-section {
    margin-top: 10rem;
}

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
    color: #fff;
    text-decoration: none;
    position: relative;
    box-shadow: 0 12px 25px rgba(0,0,0,.12);
    transition: .25s;
}

.student-card:hover {
    transform: translateY(-6px);
}

.card-text {
    font-weight: 800;
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
    .student-card-grid { grid-template-columns: repeat(2, 220px); }
}
@media (max-width: 520px) {
    .student-card-grid { grid-template-columns: 1fr; }
}
</style>

<main class="container-fluid px-4">
<div class="student-wrapper">

    <!-- BANNER -->
    <div class="student-directory-banner">
        <img src="../report.png" alt="Report by Building">
    </div>

    <!-- BUILDING SECTION -->
    <div class="student-section">
        <div class="student-card-grid">

<?php
$sql = "
SELECT 
    b.building_id,
    b.building_name,
    COUNT(r.report_id) AS total_reports
FROM building b
LEFT JOIN report r 
    ON b.building_id = r.building_id
WHERE b.status = 'Active'
GROUP BY b.building_id, b.building_name
ORDER BY b.building_name ASC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $buildingId   = (int)$row['building_id'];
        $buildingName = strtoupper($row['building_name']);
        $reportCount  = (int)$row['total_reports'];

        echo "
        <a href='report_list.php?building_id=$buildingId' class='student-card'>
            <span class='card-text'>
                $buildingName<br>
                <small style='font-size:.85rem; font-weight:600; opacity:.9'>
                    $reportCount Reports
                </small>
            </span>
            <img src='../building-logo.png' alt='$buildingName'>
        </a>
        ";
    }
} else {
    echo "<p class='text-center fw-bold'>No reports available</p>";
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
