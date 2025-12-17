<?php
include("../config/config.php");

$building_id = $_GET['building_id'] ?? '';

if (!$building_id) exit;

$q = $conn->prepare("
    SELECT block_id, block_name
    FROM block
    WHERE building_id = ?
");
$q->bind_param("i", $building_id);
$q->execute();
$r = $q->get_result();

while ($row = $r->fetch_assoc()) {
    echo "<option value='{$row['block_id']}'>
            {$row['block_name']}
          </option>";
}
