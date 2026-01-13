<?php
include("../config/config.php");

/* ======================
   GET BOOKING ID
====================== */
$booking_id = $_GET['id'] ?? '';

// If invalid ID → back to booking list
if ($booking_id === '') {
    header("Location: /booking_facility/booking_facility.php");
    exit;
}

/* ======================
   FETCH BOOKING WITH JOINS
====================== */
$sql = "SELECT 
            bf.booking_facility_id,
            bf.facility_id,
            bf.student_id,
            bf.staff_id,
            bf.start_date,
            bf.end_date,
            bf.status,
            bf.notes,
            f.facility_name,
            b.block_name,
            bd.building_name,
            s.full_name as student_name,
            st.full_name as staff_name
        FROM booking_facility bf
        LEFT JOIN facility f ON bf.facility_id = f.facility_id
        LEFT JOIN block b ON f.block_id = b.block_id
        LEFT JOIN building bd ON b.building_id = bd.building_id
        LEFT JOIN student s ON bf.student_id = s.student_id
        LEFT JOIN staff st ON bf.staff_id = st.staff_id
        WHERE bf.booking_facility_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error");
}

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><h4>Booking not found</h4></div>";
    exit;
}

$booking = $result->fetch_assoc();

function s($arr, $key, $default = '') {
    return isset($arr[$key]) ? $arr[$key] : $default;
}

$back_url = "/booking_facility/booking_facility.php";
?>

<?php include("../page/header.php"); ?>

<main>
<style>
/* ===== Booking Details Styles ===== */
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
.booking-title {
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
.booking-back {
    margin-bottom:.75rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
}
.status-pending { background-color: #fff3cd; color: #856404; }
.status-approved { background-color: #d4edda; color: #155724; }
.status-rejected { background-color: #f8d7da; color: #721c24; }
.status-cancelled { background-color: #e2e3e5; color: #383d41; }
.status-completed { background-color: #d1ecf1; color: #0c5460; }

.notes-section {
    grid-column: 1 / -1;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin-top: 1rem;
}
</style>

<div class="container-fluid">

    <div class="booking-back">
        <a href="<?= $back_url ?>" class="text-decoration-none text-dark">
            ← Back to list
        </a>
    </div>

    <div class="profile-shell">

        <div class="profile-top">
            <div class="booking-title">
                Booking #<?= htmlspecialchars(s($booking,'booking_facility_id')) ?>
            </div>

            <div class="edit-button-wrap">

                <a href="edit_booking_facility.php?id=<?= s($booking,'booking_facility_id') ?>"
                   class="btn btn-primary">
                    Edit
                </a>

                <form method="post" action="delete_booking_facility.php"
                      onsubmit="return confirm('Are you sure you want to delete this booking?');">
                    <input type="hidden" name="booking_facility_id" value="<?= s($booking,'booking_facility_id') ?>">
                    <button type="submit" class="btn btn-danger">
                        Delete
                    </button>
                </form>

            </div>
        </div>

        <div class="profile-details">

            <div>
                <div class="detail-title">Booking ID</div>
                <div class="detail-value"><?= s($booking,'booking_facility_id') ?></div>
            </div>

            <div>
                <div class="detail-title">Status</div>
                <div class="detail-value">
                    <?php
                    $status = s($booking,'status');
                    $statusClass = 'status-pending';
                    $statusLower = strtolower($status);
                    if ($statusLower === 'approved') {
                        $statusClass = 'status-approved';
                    } elseif ($statusLower === 'rejected') {
                        $statusClass = 'status-rejected';
                    } elseif ($statusLower === 'cancelled') {
                        $statusClass = 'status-cancelled';
                    } elseif ($statusLower === 'completed') {
                        $statusClass = 'status-completed';
                    }
                    ?>
                    <span class="status-badge <?= $statusClass ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </div>
            </div>

            <div>
                <div class="detail-title">Facility ID</div>
                <div class="detail-value"><?= htmlspecialchars(s($booking,'facility_id')) ?></div>
            </div>

            <div>
                <div class="detail-title">Facility Name</div>
                <div class="detail-value"><?= htmlspecialchars(s($booking,'facility_name', '—')) ?></div>
            </div>

            <div>
                <div class="detail-title">Building</div>
                <div class="detail-value"><?= htmlspecialchars(s($booking,'building_name', '—')) ?></div>
            </div>

            <div>
                <div class="detail-title">Block</div>
                <div class="detail-value"><?= htmlspecialchars(s($booking,'block_name', '—')) ?></div>
            </div>

            <div>
                <div class="detail-title">Student ID</div>
                <div class="detail-value"><?= s($booking,'student_id') ? htmlspecialchars(s($booking,'student_id')) : '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Student Name</div>
                <div class="detail-value"><?= s($booking,'student_name') ? htmlspecialchars(s($booking,'student_name')) : '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Staff ID</div>
                <div class="detail-value"><?= s($booking,'staff_id') ? htmlspecialchars(s($booking,'staff_id')) : '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Staff Name</div>
                <div class="detail-value"><?= s($booking,'staff_name') ? htmlspecialchars(s($booking,'staff_name')) : '—' ?></div>
            </div>

            <div>
                <div class="detail-title">Start Date & Time</div>
                <div class="detail-value">
                    <?php 
                    $start_date = s($booking,'start_date');
                    echo $start_date ? date('d/m/Y H:i', strtotime($start_date)) : '—';
                    ?>
                </div>
            </div>

            <div>
                <div class="detail-title">End Date & Time</div>
                <div class="detail-value">
                    <?php 
                    $end_date = s($booking,'end_date');
                    echo $end_date ? date('d/m/Y H:i', strtotime($end_date)) : '—';
                    ?>
                </div>
            </div>

            <?php if (s($booking,'notes')): ?>
            <div class="notes-section">
                <div class="detail-title">Notes</div>
                <div class="detail-value"><?= nl2br(htmlspecialchars(s($booking,'notes'))) ?></div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</main>

<?php include("../page/footer.php"); ?>