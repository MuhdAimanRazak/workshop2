<?php
include("../config/config.php");

$block_id = $_GET['block_id'] ?? '';

$sql = "
    SELECT DISTINCT level
    FROM room
    WHERE block_id = ?
    ORDER BY level ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $block_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    echo "<option value='{$row['level']}'>Level {$row['level']}</option>";
}
