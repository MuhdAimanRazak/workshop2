<?php
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /workshop2/building/building.php?success=1");

    exit;
}

$building_name = trim($_POST['building_name']);
$status = $_POST['status'];

if ($building_name === '') {
    die("Building name is required.");
}

/* Insert into DB */
$sql = "INSERT INTO building (building_name, status)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("ss", $building_name, $status);

if ($stmt->execute()) {
    header("Location: building.php?success=1");
    exit;
} else {
    die("Insert failed.");
}
