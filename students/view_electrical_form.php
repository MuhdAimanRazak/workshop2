<?php
// =====================
// CONFIG FIRST (WAJIB)
// =====================
include("../config/config.php");

// =====================
// GET STUDENT ID
// =====================
$student_id = $_GET['id'] ?? '';

if ($student_id === '') {
    header("Location: student.php");
    exit;
}

// =====================
// FETCH STUDENT INFO
// =====================
$student_sql = "SELECT * FROM student WHERE student_id = ?";
$stmt = $conn->prepare($student_sql);
if (!$stmt) {
    die("STUDENT QUERY FAILED: " . $conn->error);
}
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("<div class='alert alert-danger'>Student not found</div>");
}

// =====================
// FETCH HOSTEL INFO
// =====================
$roomSql = "
SELECT
    bld.building_name,
    blk.block_name,
    r.room_no,
    r.level,
    bkg.bed_no,
    bkg.status_payment
FROM booking bkg
JOIN room r       ON bkg.room_id = r.room_id
JOIN block blk    ON r.block_id = blk.block_id
JOIN building bld ON blk.building_id = bld.building_id
WHERE bkg.student_id = ?
ORDER BY bkg.booking_id DESC
LIMIT 1
";

$roomStmt = $conn->prepare($roomSql);
if (!$roomStmt) {
    die("ROOM QUERY FAILED: " . $conn->error);
}
$roomStmt->bind_param("s", $student_id);
$roomStmt->execute();
$roomInfo = $roomStmt->get_result()->fetch_assoc();
$roomStmt->close();

// =====================
// FETCH ELECTRICAL ITEMS
// =====================
$item_sql = "
SELECT 
    i.item_name,
    i.fee,
    r.quantity,
    r.brand
FROM items i
LEFT JOIN item_registration r 
    ON i.item_id = r.item_id
    AND r.student_id = ?
ORDER BY i.item_id ASC
";

$stmt2 = $conn->prepare($item_sql);
if (!$stmt2) {
    die("ITEM QUERY FAILED: " . $conn->error);
}
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$items = $stmt2->get_result();

// =====================
// LOAD HEADER AFTER LOGIC
// =====================
include("../page/header.php");
?>

<style>
/* ===============================
   PAGE / GLOBAL
================================ */
body {
    margin:0;
    font-family: Arial, Helvetica, sans-serif;
    color:#000;
}
html, body {
    background:#fff !important;
    margin:0;
    padding:0;
}
.page-bg {
    background:#f0f2f5;
    min-height:100vh;
    padding:40px 20px;
}

.form-wrapper {
    max-width:900px;
    margin:0 auto;
}

/* ===============================
   BACK BUTTON (SCREEN ONLY)
================================ */
.back-btn {
    display:inline-block;
    margin-bottom:16px;
    font-size:14px;
    font-weight:700;
    color:#6f42c1;
    text-decoration:none;
}
.back-btn:hover {
    text-decoration:underline;
}

/* ===============================
   WHITE FORM CARD
================================ */
.form-card {
    background:#fff;
    padding:30px 36px;
    border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
}

/* ===============================
   HEADER (LOGO + TITLE)
================================ */
.header-flex {
    display:flex;
    align-items:flex-start;
    gap:18px;
    margin-bottom:24px;
}

.header-flex img {
    width:110px;
}

.header-title h2 {
    margin:0;
    font-size:18px;
    font-weight:800;
    line-height:1.35;
    text-transform:uppercase;
}

.header-title p {
    margin:4px 0 0;
    font-size:12px;
    color:#333;
}

/* ===============================
   STUDENT INFO (FORM STYLE)
================================ */
.header-grid {
    display:grid;
    grid-template-columns:160px 1fr 160px 1fr;
    column-gap:22px;
    row-gap:10px;
    margin-bottom:26px;
}

.label-bold {
    font-size:13px;
    font-weight:700;
}

.info-value {
    font-size:13px;
}

/* ===============================
   SECTION TITLE
================================ */
h4 {
    margin:10px 0 12px;
    font-size:15px;
    font-weight:800;
}

/* ===============================
   ITEM TABLE
================================ */
.item-table {
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    font-size:13px;
}

.item-table th {
    text-align:left;
    padding:8px 6px;
    border-bottom:2px solid #000;
    font-weight:700;
}

.item-table td {
    padding:7px 6px;
    border-bottom:1px solid #bbb;
}

.item-table th:nth-child(1),
.item-table td:nth-child(1) { width:48%; }

.item-table th:nth-child(2),
.item-table td:nth-child(2) {
    width:8%;
    text-align:center;
}

.item-table th:nth-child(3),
.item-table td:nth-child(3) {
    width:12%;
    text-align:center;
}

.item-table th:nth-child(4),
.item-table td:nth-child(4) { width:32%; }

/* CHECK MARK */
.checkmark {
    font-weight:900;
    font-size:14px;
}

/* ===============================
   NOTE
================================ */
.note {
    margin-top:18px;
    padding-top:10px;
    border-top:1px dashed #999;
    font-size:12px;
}

/* ===============================
   PDF BUTTON (BOTTOM RIGHT)
================================ */
.pdf-footer {
    display:flex;
    justify-content:flex-end;
    margin-top:26px;
}

.btn-pdf {
    background:#6f42c1;
    color:#fff;
    border:none;
    padding:9px 18px;
    border-radius:6px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
}

.btn-pdf:hover {
    background:#59339d;
}

@media print {

    @page {
        size: A4;
        margin: 10mm;
    }

    html, body {
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        font-size:12px;
    }

    .page-bg {
        min-height:auto !important;
        padding:0 !important;
        margin:0 !important;
        background:#fff !important;
    }

    .form-wrapper {
        margin:0 !important;
        padding:0 !important;
    }

    .form-card {
        box-shadow:none !important;
        border-radius:0 !important;
        padding:0 !important;
        margin:0 !important;
    }

    /* KEEP FOOTER, BUT COMPACT */
    footer,
    .site-footer {
        margin-top:6px !important;
        padding:4px 0 !important;
        font-size:11px;
        page-break-inside: avoid !important;
    }

    /* HIDE BUTTON SAHAJA */
    .back-btn,
    .pdf-footer {
        display:none !important;
    }

    /* ELAK PAGE BREAK DALAM CONTENT */
    table,
    tr,
    td,
    th,
    .note {
        page-break-inside: avoid !important;
    }

    h2 {
        font-size:17px;
    }

    h4 {
        font-size:14px;
        margin:6px 0;
    }
}



/* ===== STUDENT INFO TABLE (SCREEN + PDF SAFE) ===== */
.info-table {
    width:100%;
    border-collapse:collapse;
    margin-bottom:26px;
    font-size:13px;
}

.info-table td {
    padding:6px 8px;
    vertical-align:top;
}

.info-table .label {
    width:18%;
    font-weight:700;
}

.info-table td:nth-child(2),
.info-table td:nth-child(4) {
    width:32%;
}

</style>

<main class="page-bg">

<div class="form-wrapper">

<a href="student_details.php?id=<?= urlencode($student_id) ?>" class="back-btn">
    ← Back
</a>


<div class="form-card">

<!-- HEADER -->
<div class="header-flex">
    <img src="../utem-logo.png" width="130">
    <div class="header-title">
        <h2>BORANG PENDAFTARAN PERALATAN ELEKTRIK PERSENDIRIAN PELAJAR</h2>
        <p>Kolej Kediaman UTeM — Pejabat Hal Ehwal Pelajar</p>
    </div>
</div>

<!-- STUDENT INFO -->
<table class="info-table">
    <tr>
        <td class="label">Nama</td>
        <td><?= htmlspecialchars($student['full_name']) ?></td>

        <td class="label">No. Matrik</td>
        <td><?= htmlspecialchars($student['student_id']) ?></td>
    </tr>

    <tr>
        <td class="label">Status</td>
        <td><?= htmlspecialchars($student['status']) ?></td>

        <td class="label">Bangunan / Bilik</td>
        <td>
            <?= $roomInfo['building_name'] ?? '—' ?>
            <?= isset($roomInfo['block_name']) ? ', '.$roomInfo['block_name'] : '' ?>
            <?= isset($roomInfo['room_no']) ? ' — '.$roomInfo['room_no'] : '' ?>
            <?= isset($roomInfo['bed_no']) ? ' (Bed '.$roomInfo['bed_no'].')' : '' ?>
        </td>
    </tr>

    <tr>
        <td class="label">No. Telefon</td>
        <td><?= htmlspecialchars($student['phone_no']) ?></td>

        <td class="label">Email</td>
        <td><?= htmlspecialchars($student['email']) ?></td>
    </tr>
</table>

<h4>Senarai Peralatan:</h4>

<table class="item-table">
<tr>
    <th>Alatan</th>
    <th class="center">✔</th>
    <th class="center">Kuantiti</th>
    <th>Jenama</th>
</tr>

<?php while($row = $items->fetch_assoc()): ?>
<tr>
    <td>
        <?= htmlspecialchars($row['item_name']) ?>
        <?= $row['fee'] > 0 ? ' (RM'.$row['fee'].')' : '' ?>
    </td>
    <td class="center">
        <?= $row['quantity'] ? '<span class="checkmark">✔</span>' : '—' ?>
    </td>
    <td class="center"><?= $row['quantity'] ?? '—' ?></td>
    <td><?= htmlspecialchars($row['brand'] ?? '—') ?></td>
</tr>
<?php endwhile; ?>
</table>

<div class="note">
<strong>Nota:</strong> Peralatan di atas adalah digunakan di kolej kediaman sahaja mengikut peraturan.
</div>

<div class="pdf-footer">
    <button onclick="window.print()" class="btn-pdf">
        ⬇ Download PDF
    </button>
</div>

</div>
</div>

</main>

<?php include("../page/footer.php"); ?>
