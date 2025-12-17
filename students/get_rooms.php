<?php
include("../config/config.php");

$block_id = $_GET['block_id'] ?? '';
$level    = $_GET['level'] ?? '';
$house    = $_GET['house'] ?? null;

if (!$block_id || !$level) exit;

if ($house) {
    // BLOCK ADA HOUSE
    $sql = "
        SELECT room_id, room_no
        FROM room
        WHERE block_id = ?
          AND level = ?
          AND no_house = ?
        ORDER BY room_no
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $block_id, $level, $house);
} else {
    // BLOCK TAK ADA HOUSE
    $sql = "
        SELECT room_id, room_no
        FROM room
        WHERE block_id = ?
          AND level = ?
          AND no_house IS NULL
        ORDER BY room_no
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $block_id, $level);
}

$stmt->execute();
$res = $stmt->get_result();

while ($r = $res->fetch_assoc()) {
    echo "<option value='{$r['room_id']}'>Room {$r['room_no']}</option>";
}
