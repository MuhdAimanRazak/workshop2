<?php
include("../config/config.php");

/* =========================
   TOTAL STUDENT
========================= */
$totalStudent = $conn->query(
    "SELECT COUNT(*) AS total FROM student"
)->fetch_assoc()['total'];

/* =========================
   BED CAPACITY
========================= */
$totalCapacity = $conn->query(
    "SELECT COUNT(*) AS total FROM bed"
)->fetch_assoc()['total'];

$availableBed = $conn->query(
    "SELECT COUNT(*) AS available FROM bed WHERE student_id IS NULL"
)->fetch_assoc()['available'];

/* =========================
   PAYMENT
========================= */
$payment = $conn->query("
    SELECT 
        SUM(status_payment='Paid') AS paid,
        SUM(status_payment='Pending') AS pending
    FROM payment
")->fetch_assoc();

/* =========================
   BUILDING OCCUPANCY
========================= */
$buildingData = [];
$q = $conn->query("
    SELECT 
        r.block_name,
        COUNT(b.bed_id) AS capacity,
        SUM(CASE WHEN b.student_id IS NOT NULL THEN 1 ELSE 0 END) AS occupied
    FROM room r
    JOIN bed b ON r.room_id = b.room_id
    GROUP BY r.block_name
");

while ($row = $q->fetch_assoc()) {
    $buildingData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hostel Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f8;
}
.container {
    padding: 2rem;
}
.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.card {
    background: white;
    padding: 1.2rem;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}
.card h2 {
    margin: 0;
    font-size: 1.8rem;
}
.card p {
    margin-top: .3rem;
    color: #666;
}
.section {
    margin-top: 2rem;
}
.progress {
    background: #eee;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
}
.progress-bar {
    background: #4CAF50;
    color: white;
    padding: .4rem;
    font-size: .85rem;
    text-align: center;
}
.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}
</style>
</head>

<body>
<div class="container">

<div class="cards">

    <div class="card">
        <h2><?= $totalStudent ?></h2>
        <p>Total Students</p>
    </div>

    <div class="card">
        <h2><?= $availableBed ?> / <?= $totalCapacity ?></h2>
        <p>Room Available</p>
    </div>

    <div class="card">
        <h2><?= $payment['paid'] ?> / <?= $totalStudent ?></h2>
        <p>Payment Made</p>
    </div>

    <div class="card">
        <h2><?= round(($payment['paid']/$totalStudent)*100) ?>%</h2>
        <p>Payment Completion</p>
    </div>

</div>

<div class="section">
<h3>Building Occupancy</h3>

<?php foreach ($buildingData as $b):
$percent = ($b['occupied'] / $b['capacity']) * 100;
?>

<strong><?= $b['block_name'] ?></strong>
(<?= $b['occupied'] ?>/<?= $b['capacity'] ?>)

<div class="progress">
    <div class="progress-bar" style="width:<?= $percent ?>%">
        <?= round($percent) ?>%
    </div>
</div>

<?php endforeach; ?>
</div>

<div class="section grid-2">

    <div class="card">
        <canvas id="buildingChart"></canvas>
    </div>

    <div class="card">
        <canvas id="paymentChart"></canvas>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const buildingLabels = <?= json_encode(array_column($buildingData,'block_name')) ?>;
    const occupiedData  = <?= json_encode(array_column($buildingData,'occupied')) ?>;
    const capacityData  = <?= json_encode(array_column($buildingData,'capacity')) ?>;

    new Chart(document.getElementById('buildingChart'), {
        type: 'bar',
        data: {
            labels: buildingLabels,
            datasets: [
                { label: 'Occupied', data: occupiedData },
                { label: 'Capacity', data: capacityData }
            ]
        }
    });

    new Chart(document.getElementById('paymentChart'), {
        type: 'pie',
        data: {
            labels: ['Paid', 'Pending'],
            datasets: [{
                data: [<?= $payment['paid'] ?>, <?= $payment['pending'] ?>]
            }]
        }
    });

});
</script>

</div>
</body>
</html>
