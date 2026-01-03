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

$building_id = $_GET['building_id'] ?? null;

if (!$building_id) {
    die('<div class="container mt-4">
            <div class="alert alert-warning">
                Building not specified.
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
   WHITE WRAPPER (FOLLOW STUDENT)
================================ */
.staff-wrapper {
    background: #ffffff;
    border-radius: 24px;
    padding: 3.5rem 3rem;
    margin-bottom: 2rem;
    padding-bottom: 12rem;
    box-shadow: 0 12px 30px rgba(0,0,0,.08);
    width: 100%;
    position: relative;
    overflow: hidden;
}

/* ===============================
   BANNER
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
   SECTION
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

/* ICON */
.staff-card img {
    width: 120px;
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

<div class="staff-wrapper">

    <!-- BANNER -->
    <div class="staff-directory-banner">
        <img src="../room-banner.png" alt="Block Directory">
    </div>

    <!-- BLOCK SECTION -->
    <div class="staff-section">
        <div class="staff-card-grid">

<?php
$sql = "
SELECT 
    bl.block_id,
    bl.block_name,
    COUNT(r.room_id) AS total_rooms
FROM block bl
LEFT JOIN room r 
    ON bl.block_id = r.block_id
WHERE bl.building_id = ?
GROUP BY bl.block_id, bl.block_name
ORDER BY bl.block_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $blockId   = (int)$row['block_id'];
        $blockName = htmlspecialchars(strtoupper($row['block_name']));
        $roomCount = (int)$row['total_rooms'];

        echo "
        <a href='room.php?block_id=$blockId' class='staff-card'>
            <span class='card-text'>
                $blockName<br>
                <small style='font-size:0.85rem; font-weight:600; opacity:0.9;'>
                    $roomCount Rooms
                </small>
            </span>
            <img src='../building-logo.png' alt='$blockName'>
        </a>
        ";
    }
} else {
    echo "<p class='text-center fw-bold'>No blocks available</p>";
}

$stmt->close();
?>

        </div>
    </div>

    <!-- DECOR -->
    <img src="../background-left.png" class="decor-left" alt="">
    <img src="../background-right.png" class="decor-right" alt="">

</div>
</main>

<?php include("../page/footer.php"); ?>
