<?php
require_once("../config/config.php");

ob_clean();
header("Content-Type: application/json");

if (!isset($_POST['report_id'], $_POST['status'])) {
    echo json_encode([
        "success" => false,
        "msg" => "Missing data"
    ]);
    exit;
}

$report_id = (int) $_POST['report_id'];
$status = $_POST['status'];
// UI -> Database enum mapping
$statusUiToDb = [
    'New'      => 'New',
    'Pending'  => 'In Progress',
    'Resolved' => 'Completed'
];

$dbStatus = $statusUiToDb[$status] ?? $status;

// ENUM ikut database
$allowed = ['New', 'In Progress', 'Completed'];

if ($report_id <= 0 || !in_array($status, $allowed)) {
    echo json_encode([
        "success" => false,
        "msg" => "Invalid status"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE report SET report_status = ? WHERE report_id = ?"
);
$stmt->bind_param("si", $dbStatus, $report_id);


if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "msg" => $stmt->error
    ]);
}
