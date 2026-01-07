<?php
include("../page/header.php");
include("../config/config.php");

/* ======================
   TOTAL BEDS
====================== */
$sqlTotalBeds = "SELECT SUM(total_bed) AS total_beds FROM room";
$totalBeds = (int)($conn->query($sqlTotalBeds)->fetch_assoc()['total_beds'] ?? 0);

/* ======================
   OCCUPIED BEDS
====================== */
$sqlOccupied = "SELECT COUNT(*) AS occupied FROM booking";
$occupiedBeds = (int)($conn->query($sqlOccupied)->fetch_assoc()['occupied'] ?? 0);

/* ======================
   AVAILABLE + RATE
====================== */
$availableBeds = max($totalBeds - $occupiedBeds, 0);
$occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

/* ======================
   OCCUPANCY BY BLOCK
====================== */
$sqlBlock = "
SELECT 
    b.block_name,
    b.student_gender,
    SUM(r.total_bed) AS total_beds,
    COUNT(k.booking_id) AS occupied
FROM block b
LEFT JOIN room r ON r.block_id = b.block_id
LEFT JOIN booking k ON k.room_id = r.room_id
GROUP BY b.block_id
ORDER BY b.block_name
";
$blockResult = $conn->query($sqlBlock);
?>

<main class="container-fluid px-4 py-4">

<style>
.report-card {
    background:#fff;
    border-radius:14px;
    padding:1.25rem;
    box-shadow:0 8px 20px rgba(0,0,0,.05);
}
.metric-grid {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:1.2rem;
    margin-bottom:2rem;
}
.metric-title {
    font-size:.7rem;
    color:#6c757d;
    text-transform:uppercase;
    font-weight:600;
}
.metric-value {
    font-size:1.6rem;
    font-weight:800;
}
.chart-title {
    font-weight:700;
    margin:1.5rem 0 .5rem;
}
.chart-wrap {
    display:flex;
    justify-content:center;
    margin-bottom:2rem;
}
table th {
    background:#f8f9fa;
}
table tbody tr {
    background-color: #ffffff !important;
}

table thead tr {
    background-color: #2ecc71 !important;  /* hijau solid */
}

table thead th {
    background-color: #cdfae0ff !important;  /* force fill */
    color: #ffffff;                        /* teks putih */
    font-weight: 700;

}


</style>

<a href="javascript:history.back()" class="btn btn-light mb-3">← Back</a>

<h4 class="mb-4">Current Student Hostel Occupancy Report</h4>

<!-- ===== SUMMARY CARDS (2x2) ===== -->
<div class="metric-grid">
    <div class="report-card">
        <div class="metric-title">Total Beds</div>
        <div class="metric-value"><?= $totalBeds ?></div>
    </div>

    <div class="report-card">
        <div class="metric-title">Beds Occupied</div>
        <div class="metric-value"><?= $occupiedBeds ?></div>
    </div>

    <div class="report-card">
        <div class="metric-title">Beds Available</div>
        <div class="metric-value"><?= $availableBeds ?></div>
    </div>

    <div class="report-card">
        <div class="metric-title">Occupancy Rate</div>
        <div class="metric-value"><?= $occupancyRate ?>%</div>
    </div>
</div>

<!-- ===== PIE CHART ===== -->
<div class="chart-title">Occupancy Rate Distribution</div>
<div class="chart-wrap">
    <canvas id="occupancyChart" width="300" height="300"></canvas>
</div>

<!-- ===== TABLE ===== -->
<h6 class="chart-title">Occupancy by Block</h6>

<div class="report-card">
<table class="table table-bordered text-center align-middle mb-0">
    <thead>
        <tr>
            <th>Block</th>
            <th>Gender</th>
            <th>Total Beds</th>
            <th>Occupied</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $blockResult->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['block_name']) ?></td>
            <td><?= htmlspecialchars($row['student_gender']) ?></td>
            <td><?= (int)$row['total_beds'] ?></td>
            <td><?= (int)$row['occupied'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

</main>

<!-- ===== CHART JS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('occupancyChart');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Occupied Beds', 'Available Beds'],
        datasets: [{
            data: [<?= $occupiedBeds ?>, <?= $availableBeds ?>],
            backgroundColor: ['#4dabf7', '#ff6b81']
        }]
    },
    options: {
        responsive: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include("../page/footer.php"); ?>
