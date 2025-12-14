<?php
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: staff.php");
    exit;
}

$staff_id = $_POST['staff_id'] ?? '';

if ($staff_id === '') {
    header("Location: staff.php");
    exit;
}

/* ======================
   DELETE STAFF
====================== */
$sql = "DELETE FROM staff WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_id);

$stmt->execute();
$stmt->close();

/* ======================
   REDIRECT
====================== */
header("Location: staff.php");
exit;
