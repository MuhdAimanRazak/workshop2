<?php
include("../config/config.php");

/* ONLY ACCEPT POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

/* GET POST DATA */
$building_id      = $_POST['building_id'] ?? 0;
$block_name       = trim($_POST['block_name'] ?? '');
$total_level      = $_POST['total_level'] ?? 0;
$student_gender   = $_POST['student_gender'] ?? '';
$person_in_charge = trim($_POST['person_in_charge'] ?? '');

/* VALIDATION */
if ($building_id == 0 || $block_name === '' || $total_level == 0 || $student_gender === '') {
    die("Invalid form data.");
}

/* INSERT */
$sql = "
    INSERT INTO block (building_id, block_name, total_level, student_gender, person_in_charge)
    VALUES (?, ?, ?, ?, ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isiss",
    $building_id,
    $block_name,
    $total_level,
    $student_gender,
    $person_in_charge
);
$stmt->execute();

/* REDIRECT */
header("Location: manage_blocks.php?id=" . $building_id);
exit;
