<?php
include("../config/config.php");

/* ======================
   GET FACILITY ID (STRING)
====================== */
$facility_id = $_GET['id'] ?? '';

// If invalid ID → back to facility list
if ($facility_id === '') {
    header("Location: /facility/facility.php");
    exit;
}

/* ======================
   FETCH FACILITY WITH 3-TABLE JOIN
====================== */
$sql = "SELECT 
            f.facility_id,
            f.facility_name,
            f.status,
            f.start_date,
            f.end_date,
            b.block_name,
            bd.building_name
        FROM facility f
        INNER JOIN block b ON f.block_id = b.block_id
        INNER JOIN building bd ON b.building_id = bd.building_id
        WHERE f.facility_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error");
}

/* 🔴 facility_id IS STRING */
$stmt->bind_param("s", $facility_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Facility not found</h4></div>";
    exit;
}

$facility = $result->fetch_assoc();

function s($arr, $key, $default = '') {
    return isset($arr[$key]) ? $arr[$key] : $default;
}

$back_url = "/facility/facility.php";
?>

<?php include("../page/header.php"); ?>

<main>
<style>
    html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}
/* ===== Facility Details Styles ===== */
.container-fluid { padding: 2.5rem; }
.profile-shell {
    background:#fff;
    border-radius:12px;
    padding:2.25rem;
    box-shadow:0 8px 25px rgba(0,0,0,.05);
}
.profile-top {
    display:flex;
    align-items:center;
    justify-content: space-between;
    position:relative;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
}
.facility-name {
    font-size:1.75rem;
    font-weight:800;
    color: #2a2a8c;
}
.edit-button-wrap {
    display: flex;
    gap: 0.5rem;
}

.profile-details {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:1.5rem 4rem;
    margin-top:2rem;
}
.detail-title {
    font-size:.75rem;
    font-weight:800;
    color:#2a2a8c;
    text-transform:uppercase;
    margin-bottom: 0.5rem;
}
.detail-value {
    font-weight:600;
    font-size: 1rem;
}
.facility-back {
    margin-bottom:.75rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
}
.status-available { background-color: #d4edda; color: #155724; }
.status-occupied { background-color: #fff3cd; color: #856404; }
.status-maintenance { background-color: #f8d7da; color: #721c24; }
</style>

<div class="container-fluid">

    <div class="facility-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to list
        </a>
    </div>

    <div class="profile-shell">

        <div class="profile-top">
            <div class="facility-name">
                <?= htmlspecialchars(s($facility,'facility_name')) ?>
            </div>

            <div class="edit-button-wrap">

                <a href="edit_facility.php?id=<?= s($facility,'facility_id') ?>"
                   class="btn btn-primary">
                    Edit
                </a>

            </div>
        </div>

        <div class="profile-details">

            <div>
                <div class="detail-title">Facility ID</div>
                <div class="detail-value"><?= s($facility,'facility_id') ?></div>
            </div>

            <div>
                <div class="detail-title">Building Name</div>
                <div class="detail-value"><?= htmlspecialchars(s($facility,'building_name')) ?></div>
            </div>

            <div>
                <div class="detail-title">Block Name</div>
                <div class="detail-value"><?= htmlspecialchars(s($facility,'block_name')) ?></div>
            </div>

            <div>
                <div class="detail-title">Facility Name</div>
                <div class="detail-value"><?= htmlspecialchars(s($facility,'facility_name')) ?></div>
            </div>

            <div>
                <div class="detail-title">Status</div>
                <div class="detail-value">
                    <?php
                    $status = s($facility,'status');
                    $statusClass = 'status-available';
                    if (strtolower($status) === 'occupied') {
                        $statusClass = 'status-occupied';
                    } elseif (strtolower($status) === 'maintenance') {
                        $statusClass = 'status-maintenance';
                    }
                    ?>
                    <span class="status-badge <?= $statusClass ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </div>
            </div>

            <div>
                <div class="detail-title">Start Date</div>
                <div class="detail-value">
                    <?php 
                    $start_date = s($facility,'start_date');
                    echo $start_date ? date('F j, Y', strtotime($start_date)) : '—';
                    ?>
                </div>
            </div>

            <div>
                <div class="detail-title">End Date</div>
                <div class="detail-value">
                    <?php 
                    $end_date = s($facility,'end_date');
                    echo $end_date ? date('F j, Y', strtotime($end_date)) : '—';
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>
</main>

<?php include("../page/footer.php"); ?>