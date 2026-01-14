<?php
include("../page/header.php");
include("../config/config.php");

$selectedBuilding = $_GET['building'] ?? '';

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
$occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0;
/* ======================
   OCCUPANCY BY BUILDING
====================== */
$sqlBuilding = "
SELECT 
    g.building_name,
    SUM(r.total_bed) AS total_beds,
    COUNT(b.booking_id) AS occupied
FROM building g
LEFT JOIN block bl ON bl.building_id = g.building_id
LEFT JOIN room r ON r.block_id = bl.block_id
LEFT JOIN booking b ON b.room_id = r.room_id
GROUP BY g.building_id
ORDER BY g.building_name
";
$buildingResult = $conn->query($sqlBuilding);
$sqlBuildings = "SELECT building_id, building_name FROM building ORDER BY building_name";
$buildings = $conn->query($sqlBuildings);

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
";

if ($selectedBuilding !== '') {
    $sqlBlock .= " WHERE b.building_id = ?";
}

$sqlBlock .= "
GROUP BY b.block_id
ORDER BY b.block_name
";
if ($selectedBuilding !== '') {
    $stmt = $conn->prepare($sqlBlock);
    $stmt->bind_param("i", $selectedBuilding);
    $stmt->execute();
    $blockResult = $stmt->get_result();
} else {
    $blockResult = $conn->query($sqlBlock);
}

?>

<style>
body { background:#f5f7ff; }

.container-fluid {
    background:#fff;
    border-radius:26px;
    padding:32px;
    box-shadow:0 20px 50px rgba(0,0,0,.08);
}

/* Welcome bar */
.welcome-bar {
    background:linear-gradient(135deg,#f6f7ff,#eef2ff);
    border-radius:22px;
    padding:22px 30px;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}

/* Stat cards */
.card.stat {
    border-radius:20px;
    background:linear-gradient(135deg,#8be6b0,#237be7);
    color:#fff;
    box-shadow:0 14px 32px rgba(35,123,231,.25);
}
.card.stat .card-body {
    min-height:140px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}

/* Normal cards */
.card.shadow-sm {
    border-radius:22px;
    box-shadow:0 14px 32px rgba(0,0,0,.08);
}

table thead th {
    background:#2ecc71;
    color:#fff;
    font-weight:700;
}
.chart-wrap {
    max-width: 220px;
    margin: 0 auto;
}

.chart-card {
    max-height: 360px;
}

/* Remove zebra striping (grey rows) */
.table tbody tr {
    background-color: #ffffff !important;
}

/* Optional: hover effect lembut sahaja */
.table tbody tr:hover {
    background-color: #f5f7ff !important;
}

/* Card warna untuk Occupancy by Block */
.card.building-card {
    background: linear-gradient(180deg, #f1f5ff 0%, #ffffff 40%);
    border: 1px solid #dbe4ff;
}
.card.building-card table thead th {
    background: #f8f9ff;
    color: #364fc7;
    font-weight: 600;
}
.card.building-card h5 {
    border-bottom: 2px solid #dbe4ff;
    padding-bottom: 6px;
    margin-bottom: 14px;
}



/* Tajuk dalam card */
.card.building-card h5 {
    color: #364fc7;
    font-weight: 700;
}

/* Occupancy cards accent */
.card.occupancy-card {
    border-left: 5px solid #4263eb;
}

/* Tajuk card */
.card.occupancy-card h5 {
    color: #4263eb;
    font-weight: 700;
}

.card.occupancy-card table thead th {
    background: #f8f9ff;
    color: #364fc7;
    font-weight: 600;
}

</style>

<div class="container-fluid mt-4">

<!-- ===== WELCOME BAR ===== -->
<div class="welcome-bar d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-semibold mb-1">Current Student Hostel Occupancy Report</h4>
        <small class="text-muted">Real-time overview of hostel capacity usage</small>
    </div>
    <span class="badge bg-primary">Live Data</span>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="row g-4 text-center mb-4">
    <div class="col-md-3">
        <div class="card stat">
            <div class="card-body">
                <h6>Total Beds</h6>
                <h2><?= $totalBeds ?></h2>
                <small>Registered</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat">
            <div class="card-body">
                <h6>Beds Occupied</h6>
                <h2><?= $occupiedBeds ?></h2>
                <small>In Use</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat">
            <div class="card-body">
                <h6>Beds Available</h6>
                <h2><?= $availableBeds ?></h2>
                <small>Vacant</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat">
            <div class="card-body">
                <h6>Occupancy Rate</h6>
                <h2><?= $occupancyRate ?>%</h2>
                <small>Utilization</small>
            </div>
        </div>
    </div>
</div>

<!-- ===== CHART + INSIGHT ===== -->
<div class="row justify-content-center mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h5 class="mb-3">Occupancy Distribution</h5>
                <div class="chart-wrap">
                    <canvas id="occupancyChart" width="150" height="150"></canvas>
                </div>
                <p class="text-muted mt-2">
                    <?= $occupiedBeds ?> occupied · <?= $availableBeds ?> available
                </p>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-sm mb-4 building-card">

    <div class="card-body">
        <h5 class="mb-3">Occupancy by Building</h5>

        <table class="table table-bordered text-center align-middle mb-0">
            <thead>
                <tr>
                    <th>Building</th>
                    <th>Total Beds</th>
                    <th>Occupied</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php while($b = $buildingResult->fetch_assoc()):
                $rate = $b['total_beds'] > 0
                    ? round(($b['occupied'] / $b['total_beds']) * 100, 2)
                    : 0;
            ?>
                <tr>
                    <td><?= htmlspecialchars($b['building_name']) ?></td>
                    <td><?= (int)$b['total_beds'] ?></td>
                    <td><?= (int)$b['occupied'] ?></td>
                    <td><?= $rate ?>%</td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- ===== TABLE ===== -->
<?php while ($bld = $buildings->fetch_assoc()): ?>

<div class="card shadow-sm mb-4 occupancy-card">


    <div class="card-body">
        <h5 class="mb-3">
            Occupancy by Block – <?= htmlspecialchars($bld['building_name']) ?>
        </h5>

        <?php
        $sqlBlock = "
        SELECT 
            b.block_name,
            b.student_gender,
            SUM(r.total_bed) AS total_beds,
            COUNT(k.booking_id) AS occupied
        FROM block b
        LEFT JOIN room r ON r.block_id = b.block_id
        LEFT JOIN booking k ON k.room_id = r.room_id
        WHERE b.building_id = ?
        GROUP BY b.block_id
        ORDER BY b.block_name
        ";

        $stmt = $conn->prepare($sqlBlock);
        $stmt->bind_param("i", $bld['building_id']);
        $stmt->execute();
        $blockResult = $stmt->get_result();
        ?>

        <table class="table table-bordered text-center align-middle mb-0">
            <thead>
                <tr>
                    <th>Block</th>
                    <th>Gender</th>
                    <th>Total Beds</th>
                    <th>Occupied</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $blockResult->fetch_assoc()):
                $rate = $row['total_beds'] > 0
                    ? round(($row['occupied'] / $row['total_beds']) * 100, 2)
                    : 0;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['block_name']) ?></td>
                    <td><?= htmlspecialchars($row['student_gender']) ?></td>
                    <td><?= (int)$row['total_beds'] ?></td>
                    <td><?= (int)$row['occupied'] ?></td>
                    <td><?= $rate ?>%</td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endwhile; ?>

</div>

<!-- ===== CHART JS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const centerText = {
    id: 'centerText',
    beforeDraw(chart) {
        const { width, height, ctx } = chart;
        ctx.restore();
        ctx.font = 'bold 12px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('<?= $occupancyRate ?>%', width / 2, height / 2);
        ctx.save();
    }
};

new Chart(document.getElementById('occupancyChart'), {
    type: 'doughnut',
    data: {
        labels: ['Occupied Beds', 'Available Beds'],
        datasets: [{
            data: [<?= $occupiedBeds ?>, <?= $availableBeds ?>],
            backgroundColor: ['#4dabf7', '#ff6b81']
        }]
    },
    options: {
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom' }
        }
    },
    plugins: [centerText]
});
</script>

<?php include("../page/footer.php"); ?>
