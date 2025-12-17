<?php
include("../config/config.php");

$room_id = $_GET['room_id'] ?? '';
if (!$room_id) exit;

$beds = [1,2];

$q = $conn->prepare("
    SELECT bed_no
    FROM booking
    WHERE room_id = ?
");
$q->bind_param("s", $room_id);
$q->execute();
$r = $q->get_result();

$used = [];
while ($row = $r->fetch_assoc()) {
    $used[] = $row['bed_no'];
}

foreach ($beds as $bed) {
    if (!in_array($bed, $used)) {
        echo "<option value='{$room_id}|{$bed}'>
                Bed {$bed}
              </option>";
    }
}
