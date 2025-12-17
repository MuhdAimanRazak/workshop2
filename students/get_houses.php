<?php
include("../config/config.php");

$block_id = $_GET['block_id'] ?? '';
$level    = $_GET['level'] ?? '';

if (!$block_id || !$level) exit;

// ambil house yang WUJUD sahaja
$sql = "
    SELECT DISTINCT no_house
    FROM room
    WHERE block_id = ?
      AND level = ?
      AND no_house IS NOT NULL
    ORDER BY 
      CAST(SUBSTRING_INDEX(no_house, '-', -1) AS UNSIGNED)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $block_id, $level);
$stmt->execute();
$res = $stmt->get_result();

while ($r = $res->fetch_assoc()) {
    echo "<option value='{$r['no_house']}'>House {$r['no_house']}</option>";
}
